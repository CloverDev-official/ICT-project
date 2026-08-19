import * as echarts from "echarts/core";
import { LineChart } from "echarts/charts";
import {
    GridComponent,
    ToolboxComponent,
    TooltipComponent,
} from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";

echarts.use([
    LineChart,
    GridComponent,
    ToolboxComponent,
    TooltipComponent,
    CanvasRenderer,
]);

const months = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember",
];

function optionFor(payload) {
    return {
        tooltip: { trigger: "axis" },
        toolbox: { show: true, feature: { saveAsImage: {} } },
        grid: { left: 40, right: 20, bottom: 40, top: 60 },
        xAxis: { type: "category", boundaryGap: false, data: months },
        yAxis: { type: "value", name: "Jumlah Murid" },
        series: [{
            name: `Jumlah Hadir ${payload.year}`,
            type: "line",
            smooth: true,
            data: payload.data,
            lineStyle: { width: 3 },
            itemStyle: { color: "#6366f1" },
            areaStyle: { opacity: 0.2 },
        }],
    };
}

export function initRekapAbsenMuridChart(initialData) {
    const dom = document.getElementById("chart-rekap-absen-murid");
    if (!dom) return;

    const chart = echarts.getInstanceByDom(dom) ?? echarts.init(dom);
    const apply = (payload) => chart.setOption(optionFor(payload), true);
    apply(initialData);

    if (dom.dataset.chartEventsBound) return;
    dom.dataset.chartEventsBound = "true";
    addEventListener("chart-rekap-updated", (event) => {
        if (event.detail?.chart) apply(event.detail.chart);
    });
    addEventListener("resize", () => chart.resize());
}
