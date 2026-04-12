@extends('layouts.app')

@section('content_title', 'Forecast Penjualan Ayam')

@section('content')

    <div class="row">
        <div class="col-12">

            {{-- CARD GRAFIK --}}
            <div class="card card-primary">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Forecast Penjualan
                    </h3>

                    <div class="d-flex" style="gap:8px">

                        <input type="number" id="days" value="365" min="1" max="365"
                            class="form-control form-control-sm" style="width:90px">

                        <button id="btnTrain" class="btn btn-warning btn-sm">
                            <i class="fas fa-brain"></i> Train
                        </button>

                        <button id="btnGenerate" class="btn btn-light btn-sm">
                            Generate
                        </button>

                    </div>
                </div>


                <div class="card-body">

                    {{-- FILTER --}}
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
                            <button onclick="loadChart()" class="btn btn-info btn-block">
                                Filter
                            </button>
                        </div>

                    </div>


                    <div id="loadingBox" class="alert alert-info d-none text-center">
                        Memproses...
                    </div>

                    <canvas id="forecastChart" height="100"></canvas>

                </div>
            </div>


            {{-- TABEL --}}
            <div class="card mt-3">

                <div class="card-header">
                    <h3 class="card-title">Data Forecast</h3>
                </div>

                <div class="card-body table-responsive">

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Aktual</th>
                                <th>Prediksi</th>
                                <th>Min</th>
                                <th>Max</th>
                            </tr>
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
        let chart


        // =========================
        // LOAD CHART DATA
        // =========================
        async function loadChart() {

            const start = document.getElementById("start").value
            const end = document.getElementById("end").value

            const res = await fetch(`/forecast/data?start=${start}&end=${end}`)
            const data = await res.json()

            const labels = data.map(d =>
                new Date(d.tanggal).toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "2-digit"
                })
            )

            const aktual  = data.map(d => d.aktual)
            const pred    = data.map(d => d.prediksi)
            const min     = data.map(d => d.lower)
            const max     = data.map(d => d.upper)

            if (chart) chart.destroy()

            chart = new Chart(document.getElementById("forecastChart"), {
                type: "line",
                data: {
                    labels,
                    datasets: [
                        {
                            label: "Aktual",
                            data: aktual,
                            borderColor: "rgba(40,167,69,1)",
                            backgroundColor: "rgba(40,167,69,0.1)",
                            borderWidth: 2,
                            tension: .3,
                            pointRadius: 0,
                            spanGaps: false
                        },
                        {
                            label: "Prediksi",
                            data: pred,
                            borderColor: "rgba(0,123,255,1)",
                            backgroundColor: "rgba(0,123,255,0.05)",
                            borderWidth: 2,
                            tension: .3,
                            pointRadius: 0
                        },
                        {
                            label: "Min",
                            data: min,
                            borderColor: "rgba(255,193,7,0.6)",
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false
                        },
                        {
                            label: "Max",
                            data: max,
                            borderColor: "rgba(220,53,69,0.6)",
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: "top" },
                        tooltip: { mode: "index", intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            })

            renderTable(data)
        }



        // =========================
        // TABLE RENDER
        // =========================
        function renderTable(data) {

            const tbody = document.getElementById("forecastTable")

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>`
                return
            }

            tbody.innerHTML = data.map(r => `
                <tr>
                <td>${new Date(r.tanggal).toLocaleDateString("id-ID")}</td>
                <td>${r.aktual !== null ? r.aktual.toLocaleString() : '-'}</td>
                <td>${r.prediksi.toLocaleString()}</td>
                <td>${r.lower.toLocaleString()}</td>
                <td>${r.upper.toLocaleString()}</td>
                </tr>
                `).join("")
        }



        // =========================
        // TRAIN MODEL
        // =========================
        document.getElementById("btnTrain").onclick = async () => {

            const box = document.getElementById("loadingBox")
            box.classList.remove("d-none", "alert-danger", "alert-success")
            box.classList.add("alert-info")
            box.innerText = "Training model..."

            try {

                const res = await fetch("{{ route('forecast.train') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })

                const json = await res.json()

                box.classList.remove("alert-info")
                box.classList.add("alert-success")
                box.innerText = "Training dimulai di background"

            } catch {

                box.classList.remove("alert-info")
                box.classList.add("alert-danger")
                box.innerText = "Gagal training"

            }

            setTimeout(() => box.classList.add("d-none"), 3000)

        }



        // =========================
        // GENERATE FORECAST
        // =========================
        document.getElementById("btnGenerate").onclick = async () => {

            const days = document.getElementById("days").value
            const box = document.getElementById("loadingBox")

            box.classList.remove("d-none")
            box.innerText = "Generate forecast..."

            try {

                const res = await fetch("{{ route('forecast.generate') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        days
                    })
                })

                const json = await res.json()

                if (!res.ok) throw new Error(json.message ?? "Generate gagal")

                await loadChart()

                box.innerText = "Forecast berhasil dibuat (" + (json.count ?? 0) + " data)"

            } catch (err) {

                box.classList.remove("alert-info")
                box.classList.add("alert-danger")
                box.innerText = err.message || "Generate gagal"

            }

            setTimeout(() => box.classList.add("d-none"), 2000)

        }



        // =========================
        // AUTO LOAD
        // =========================
        document.addEventListener("DOMContentLoaded", () => {
            // Default tampilkan perbandingan aktual vs prediksi tahun 2025
            document.getElementById("start").value = "2025-01-01"
            document.getElementById("end").value   = "2025-12-31"

            loadChart()
        })
    </script>
@endpush
