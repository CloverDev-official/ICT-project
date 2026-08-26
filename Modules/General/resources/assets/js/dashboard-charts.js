import * as echarts from "echarts/core";
import { BarChart, PieChart } from "echarts/charts";
import {
    GridComponent,
    LegendComponent,
    TooltipComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";

echarts.use([
    BarChart,
    PieChart,
    GridComponent,
    LegendComponent,
    TooltipComponent,
    CanvasRenderer,
]);

const fallbackMurid = [
    { value: 30, name: "Hadir", color: "#22c55e" },
    { value: 5, name: "Izin", color: "#eab308" },
    { value: 3, name: "Sakit", color: "#3b82f6" },
    { value: 2, name: "Alpa", color: "#ef4444" },
];

const fallbackGuru = [
    { value: 15, name: "Hadir", color: "#22c55e" },
    { value: 2, name: "Izin", color: "#eab308" },
    { value: 1, name: "Sakit", color: "#3b82f6" },
    { value: 2, name: "Alpa", color: "#ef4444" },
];

function createChart(dom) {
    echarts.getInstanceByDom(dom)?.dispose();
    return echarts.init(dom);
}

function toSeries(items) {
    return items.map(({ value, name, color }) => ({
        value,
        name,
        itemStyle: { color },
    }));
}

function renderDonut(dom, data, fallback, name) {
    if (!dom) return;

    const source = data?.series?.length ? data.series : fallback;
    const series = toSeries(source);
    const total = data?.total ?? series.reduce((sum, item) => sum + item.value, 0);

    createChart(dom).setOption({
        tooltip: { trigger: "item" },
        legend: { bottom: "0%", left: "center" },
        graphic: {
            type: "text",
            left: "center",
            top: "center",
            style: {
                text: `${total}\nTotal`,
                textAlign: "center",
                fill: "#333",
                fontSize: 18,
                fontWeight: "bold",
            },
        },
        series: [{
            name,
            type: "pie",
            radius: ["45%", "70%"],
            itemStyle: { borderRadius: 8, borderColor: "#fff", borderWidth: 2 },
            label: { show: false },
            data: series,
        }],
    });
}

function renderAttendance(dom, data, fallback, yAxisName, color) {
    if (!dom) return;

    createChart(dom).setOption({
        tooltip: { trigger: "axis" },
        grid: { left: 40, right: 20, bottom: 40, top: 30 },
        xAxis: {
            type: "category",
            data: data?.labels?.length
                ? data.labels
                : ["01 Apr", "02 Apr", "03 Apr", "04 Apr", "05 Apr", "06 Apr", "07 Apr"],
        },
        yAxis: { type: "value", name: yAxisName },
        series: [{
            name: "Hadir",
            type: "bar",
            data: data?.values?.length ? data.values : fallback,
            itemStyle: { color, borderRadius: [6, 6, 0, 0] },
        }],
    });
}

export function initDashboardCharts(data = {}) {

    renderDonut(document.getElementById("main-murid"), data.murid, fallbackMurid, "Absensi Murid");
    renderDonut(document.getElementById("main-guru"), data.guru, fallbackGuru, "Absensi Guru");
    renderAttendance(document.getElementById("chart-tingkat-kehadiran-murid"), data.murid7, [1500, 1480, 1495, 1470, 1460, 1200, 900], "Jumlah Siswa", "#7DA0CA");
    renderAttendance(document.getElementById("chart-tingkat-kehadiran-guru"), data.guru7, [20, 19, 20, 18, 17, 15, 12], "Jumlah Guru", "#105192");
}

addEventListener("resize", () => {
    ["main-murid", "main-guru", "chart-tingkat-kehadiran-murid", "chart-tingkat-kehadiran-guru"]
        .map((id) => document.getElementById(id))
        .filter(Boolean)
        .forEach((dom) => echarts.getInstanceByDom(dom)?.resize());
});
