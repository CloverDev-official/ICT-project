document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // CHART MURID (DONUT)
    // =========================
    const muridDom = document.getElementById("main-murid");
    if (muridDom) {
        const muridChart = echarts.init(muridDom);

        muridChart.setOption({
            tooltip: { trigger: "item" },
            legend: { bottom: "0%", left: "center" },
            graphic: {
                type: "text",
                left: "center",
                top: "center",
                style: {
                    text: "40\nTotal",
                    textAlign: "center",
                    fill: "#333",
                    fontSize: 18,
                    fontWeight: "bold",
                },
            },
            series: [{
                name: "Absensi Murid",
                type: "pie",
                radius: ["45%", "70%"],
                itemStyle: {
                    borderRadius: 8,
                    borderColor: "#fff",
                    borderWidth: 2,
                },
                label: { show: false },
                data: [
                    { value: 30, name: "Hadir", itemStyle: { color: "#22c55e" } },
                    { value: 5, name: "Izin", itemStyle: { color: "#eab308" } },
                    { value: 3, name: "Sakit", itemStyle: { color: "#3b82f6" } },
                    { value: 2, name: "Alfa", itemStyle: { color: "#ef4444" } },
                ],
            }]
        });
    }

    // =========================
    // CHART GURU (DONUT)
    // =========================
    const guruDom = document.getElementById("main-guru");
    if (guruDom) {
        const guruChart = echarts.init(guruDom);

        guruChart.setOption({
            tooltip: { trigger: "item" },
            legend: { bottom: "0%", left: "center" },
            graphic: {
                type: "text",
                left: "center",
                top: "center",
                style: {
                    text: "20\nTotal",
                    textAlign: "center",
                    fill: "#333",
                    fontSize: 18,
                    fontWeight: "bold",
                },
            },
            series: [{
                name: "Absensi Guru",
                type: "pie",
                radius: ["45%", "70%"],
                itemStyle: {
                    borderRadius: 8,
                    borderColor: "#fff",
                    borderWidth: 2,
                },
                label: { show: false },
                data: [
                    { value: 15, name: "Hadir", itemStyle: { color: "#22c55e" } },
                    { value: 2, name: "Izin", itemStyle: { color: "#eab308" } },
                    { value: 1, name: "Sakit", itemStyle: { color: "#3b82f6" } },
                    { value: 2, name: "Alfa", itemStyle: { color: "#ef4444" } },
                ],
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

});