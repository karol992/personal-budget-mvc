/**
 * ---------------------------------------
 * This demo was created using amCharts 4.
 *
 * For more information visit:
 * https://www.amcharts.com/
 *
 * Documentation is available at:
 * https://www.amcharts.com/docs/v4/
 * ---------------------------------------
 */

if (document.getElementById("chartdiv")) {
	//Apply a theme
	am4core.useTheme(am4themes_kelly);

	//Create chart instance
	var chart = am4core.create("chartdiv", am4charts.PieChart);

	//Add data
	function loadChartData(expenses) {
		var step;
		for (step=0; step<expenses.length; step++) {
			chart.data.push({"category": expenses[step].name, "value": expenses[step].sum});
		}
	}
	loadChartData(onload_expenses);

	// Add example data 
	/*chart.data = [{
	  "category": "Mieszanie",
	  "value": 1450.00
	}, {
	  "category": "Transport",
	  "value": 412.35
	}, {
	  "category": "Telekomunikacja",
	  "value": 140.00
	}];*/

	// Add and configure Series
	var pieSeries = chart.series.push(new am4charts.PieSeries());
	pieSeries.dataFields.value = "value";
	pieSeries.dataFields.category = "category";

	// Disable ticks and labels
	pieSeries.labels.template.disabled = true;
	pieSeries.ticks.template.disabled = true;

	// Disable tooltips
	//pieSeries.slices.template.tooltipText = "";

	// Create legend
	chart.legend = new am4charts.Legend();

	// Create a separate container to put legend in
	var legendContainer = am4core.create("legenddiv", am4core.Container);
	legendContainer.width = am4core.percent(100);
	legendContainer.height = am4core.percent(100);
	chart.legend.parent = legendContainer;

	legendContainer.itemValueText="";
}