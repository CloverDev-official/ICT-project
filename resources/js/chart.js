function initCharts() {

    // =========================
    // DONUT CHART MURID
    // =========================
    const muridDom = document.getElementById("main-murid");

    if (muridDom) {

        const muridChart = echarts.init(muridDom);

        muridChart.setOption({

            tooltip: { trigger: "item" },

            legend: {
                bottom: "0%",
                left: "center"
            },

            graphic: {
                type: "text",
                left: "center",
                top: "center",
                style: {
                    text: "40\nTotal",
                    textAlign: "center",
                    fill: "#333",
                    fontSize: 18,
                    fontWeight: "bold"
                }
            },

            series: [{
                name: "Absensi Murid",
                type: "pie",
                radius: ["45%", "70%"],

                itemStyle: {
                    borderRadius: 8,
                    borderColor: "#fff",
                    borderWidth: 2
                },

                label: { show: false },

                data: [
                    { value: 30, name: "Hadir", itemStyle: { color: "#22c55e" } },
                    { value: 5, name: "Izin", itemStyle: { color: "#eab308" } },
                    { value: 3, name: "Sakit", itemStyle: { color: "#3b82f6" } },
                    { value: 2, name: "Alfa", itemStyle: { color: "#ef4444" } }
                ]
            }]
        });
    }


    // =========================
    // DONUT CHART GURU
    // =========================
    const guruDom = document.getElementById("main-guru");

    if (guruDom) {

        const guruChart = echarts.init(guruDom);

        guruChart.setOption({

            tooltip: { trigger: "item" },

            legend: {
                bottom: "0%",
                left: "center"
            },

            graphic: {
                type: "text",
                left: "center",
                top: "center",
                style: {
                    text: "20\nTotal",
                    textAlign: "center",
                    fill: "#333",
                    fontSize: 18,
                    fontWeight: "bold"
                }
            },

            series: [{
                name: "Absensi Guru",
                type: "pie",
                radius: ["45%", "70%"],

                itemStyle: {
                    borderRadius: 8,
                    borderColor: "#fff",
                    borderWidth: 2
                },

                label: { show: false },

                data: [
                    { value: 15, name: "Hadir", itemStyle: { color: "#22c55e" } },
                    { value: 2, name: "Izin", itemStyle: { color: "#eab308" } },
                    { value: 1, name: "Sakit", itemStyle: { color: "#3b82f6" } },
                    { value: 2, name: "Alfa", itemStyle: { color: "#ef4444" } }
                ]
            }]
        });
    }


    // =========================
    // CHART 7 HARI MURID
    // =========================
    const murid7 = document.getElementById("chart-tingkat-kehadiran-murid");

    if (murid7) {

        const chart = echarts.init(murid7);

        chart.setOption({

            tooltip: { trigger: "axis" },

            grid: {
                left: 40,
                right: 20,
                bottom: 40,
                top: 30
            },

            xAxis: {
                type: "category",
                data: ["01 Apr","02 Apr","03 Apr","04 Apr","05 Apr","06 Apr","07 Apr"]
            },

            yAxis: {
                type: "value",
                name: "Jumlah Siswa"
            },

            series: [{
                name: "Hadir",
                type: "bar",
                data: [1500,1480,1495,1470,1460,1200,900],

                itemStyle: {
                    color: "#7DA0CA",
                    borderRadius: [6,6,0,0]
                }
            }]
        });
    }


    // =========================
    // CHART 7 HARI GURU
    // =========================
    const guru7 = document.getElementById("chart-tingkat-kehadiran-guru");

    if (guru7) {

        const chart = echarts.init(guru7);

        chart.setOption({

            tooltip: { trigger: "axis" },

            grid: {
                left: 40,
                right: 20,
                bottom: 40,
                top: 30
            },

            xAxis: {
                type: "category",
                data: ["01 Apr","02 Apr","03 Apr","04 Apr","05 Apr","06 Apr","07 Apr"]
            },

            yAxis: {
                type: "value",
                name: "Jumlah Guru"
            },

            series: [{
                name: "Hadir",
                type: "bar",
                data: [20,19,20,18,17,15,12],

                itemStyle: {
                    color: "#105192",
                    borderRadius: [6,6,0,0]
                }
            }]
        });
    }


    // =========================
    // LINE CHART REKAP MURID
    // =========================
    const rekapMuridDom = document.getElementById("chart-rekap-absen-murid");

    if (rekapMuridDom) {

        const rekapChart = echarts.init(rekapMuridDom);

        rekapChart.setOption({

            tooltip: { trigger: "axis" },

            toolbox: {
                show: true,
                feature: {
                    saveAsImage: {}
                }
            },

            grid: {
                left: 40,
                right: 20,
                bottom: 40,
                top: 60
            },

            xAxis: {
                type: "category",
                boundaryGap: false,
                data: [
                    "Januari","Februari","Maret","April","Mei","Juni",
                    "Juli","Agustus","September","Oktober","November","Desember"
                ]
            },

            yAxis: {
                type: "value",
                name: "Jumlah Murid"
            },

            series: [{
                name: "Jumlah Hadir",
                type: "line",
                smooth: true,
                data: [
                    120,115,130,125,140,135,
                    150,145,138,142,148,155
                ],

                lineStyle: { width: 3 },

                itemStyle: {
                    color: "#6366f1"
                },

                areaStyle: {
                    opacity: 0.2
                }
            }]
        });

        window.addEventListener("resize", () => {
            rekapChart.resize();
        });

    }

    // =========================
    // CHART KEHADIRAN PER KELAS
    // =========================
    const chartKelasDom = document.getElementById("chart-kehadiran-kelas");
    
    if (chartKelasDom) {
    
        const chartKelas = echarts.init(chartKelasDom);
    
        chartKelas.setOption({
    
            tooltip: {
                trigger: "axis"
            },
    
            legend: {
                bottom: 0,
                itemGap: 30
            },
    
            grid: {
                left: 40,
                right: 20,
                bottom: 50,
                top: 30
            },
    
            xAxis: {
                type: "category",
                data: [
                    "X PPLG A",
                    "X PPLG B",
                    "XI PPLG A",
                    "XI PPLG B",
                    "XII PPLG A",
                    "XII PPLG B"
                ]
            },
    
            yAxis: {
                type: "value"
            },
    
            series: [
    
                {
                    name: "Hadir",
                    type: "bar",
                    stack: "total",
                    data: [30, 28, 32, 29, 31, 27],
                    itemStyle: { color: "#22c55e" }
                    
                },
    
                {
                    name: "Izin",
                    type: "bar",
                    stack: "total",
                    data: [2, 3, 1, 2, 2, 1],
                    itemStyle: { color: "#eab308" }
                    
                },
    
                {
                    name: "Sakit",
                    type: "bar",
                    stack: "total",
                    data: [1, 1, 0, 1, 1, 0],
                    itemStyle: { color: "#3b82f6" }
                },
    
                {
                    name: "Alfa",
                    type: "bar",
                    stack: "total",
                    data: [1, 2, 1, 1, 0, 2],
                    itemStyle: { color: "#ef4444" }
                }
    
            ]
        });
    
        window.addEventListener("resize", () => {
            chartKelas.resize();
        });
    
    }
}



// ============================
// EVENT UNTUK LIVEWIRE SPA
// ============================

document.addEventListener("DOMContentLoaded", initCharts);
document.addEventListener("livewire:navigated", initCharts);