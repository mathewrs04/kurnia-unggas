@extends('layouts.app')
@section('content_title', 'Forecast Penjualan Ayam')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Forecast Penjualan</h3>
            </div>
            <div class="card-body">
                {{-- Filter --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Dari</label>
                        <input type="date" id="start" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Sampai</label>
                        <input type="date" id="end" class="form-control">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button onclick="loadChart()" class="btn btn-info btn-block">Filter</button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="btnEvaluate" class="btn btn-warning btn-block">Hitung Akurasi</button>
                    </div>
                </div>

                <div id="loadingBox" class="alert alert-info d-none text-center">Memproses...</div>
                <canvas id="forecastChart" height="100"></canvas>
            </div>
        </div>

        {{-- Hasil Evaluasi Akurasi --}}
        <div id="evaluationResult" class="card mt-3" style="display:none;">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">Evaluasi Akurasi</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">MAE</span>
                                <span class="info-box-number" id="mae">-</span>
                                <small>ekor</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">RMSE</span>
                                <span class="info-box-number" id="rmse">-</span>
                                <small>ekor</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning"><i class="fas fa-percent"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">MAPE</span>
                                <span class="info-box-number" id="mape">-</span>
                                <small>%</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-secondary">
                    Periode: <span id="evalStart"></span> s.d <span id="evalEnd"></span><br>
                    Data: <span id="totalData"></span> hari
                </div>
            </div>
        </div>
        
        {{-- Tabel Data --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Data Forecast</h3>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr><th>Tanggal</th><th>Aktual</th><th>Prediksi</th><th>Min</th><th>Max</th></tr>
                    </thead>
                    <tbody id="forecastTable"></tbody>
                </table>
            </div>
        </div>

        
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chart;

    // Ambil data dan gambar grafik
    async function loadChart() {
        const start = document.getElementById('start').value;
        const end = document.getElementById('end').value;
        const res = await fetch(`/forecast/data?start=${start}&end=${end}`);
        const data = await res.json();

        const labels = data.map(d => new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit'}));
        const aktual = data.map(d => d.aktual);
        const pred = data.map(d => d.prediksi);
        const min = data.map(d => d.lower);
        const max = data.map(d => d.upper);

        if (chart) chart.destroy();
        chart = new Chart(document.getElementById('forecastChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Aktual', data: aktual, borderColor: 'green', backgroundColor: 'rgba(40,167,69,0.1)', borderWidth: 2 },
                    { label: 'Prediksi', data: pred, borderColor: 'blue', backgroundColor: 'rgba(0,123,255,0.05)', borderWidth: 2 },
                    { label: 'Min', data: min, borderColor: 'orange', pointRadius: 0, fill: false },
                    { label: 'Max', data: max, borderColor: 'red', pointRadius: 0, fill: false }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Isi tabel
        const tbody = document.getElementById('forecastTable');
        if (!data.length) tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
        else tbody.innerHTML = data.map(r => `
            <tr>
                <td>${new Date(r.tanggal).toLocaleDateString('id-ID')}</td>
                <td>${r.aktual !== null ? r.aktual.toLocaleString() : '-'}</td>
                <td>${r.prediksi.toLocaleString()}</td>
                <td>${r.lower.toLocaleString()}</td>
                <td>${r.upper.toLocaleString()}</td>
            </tr>
        `).join('');
    }

    // Hitung akurasi
    document.getElementById('btnEvaluate').onclick = async () => {
        const start = document.getElementById('start').value;
        const end = document.getElementById('end').value;
        if (!start || !end) return alert('Pilih tanggal mulai dan selesai!');

        const box = document.getElementById('loadingBox');
        box.classList.remove('d-none');
        try {
            const res = await fetch(`/forecast/evaluate?start=${start}&end=${end}`);
            const json = await res.json();
            if (json.error) throw new Error(json.error);
            document.getElementById('mae').innerText = json.mae.toLocaleString();
            document.getElementById('rmse').innerText = json.rmse.toLocaleString();
            document.getElementById('mape').innerText = json.mape.toLocaleString();
            document.getElementById('totalData').innerText = json.total_data;
            document.getElementById('evalStart').innerText = json.start;
            document.getElementById('evalEnd').innerText = json.end;
            document.getElementById('evaluationResult').style.display = 'block';
        } catch (err) {
            alert('Gagal: ' + err.message);
        } finally {
            box.classList.add('d-none');
        }
    };

    // Saat halaman siap, tampilkan data tahun 2025
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('start').value = '2025-01-01';
        document.getElementById('end').value = '2025-12-31';
        loadChart();
    });
</script>
@endpush