<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <script src="{{ asset('js/trirand/i18n/grid.locale-en.js') }}" type="text/javascript"></script>

    <link href="{{ asset('Stimulsoft Report/2021.3.6/css/stimulsoft.viewer.office2013.whiteblue.css') }}" rel="stylesheet">
    <link href="{{ asset('Stimulsoft Report/2021.3.6/css/stimulsoft.designer.office2013.whiteblue.css') }}" rel="stylesheet">
    <script src="{{ asset('Stimulsoft Report/2021.3.6/scripts/stimulsoft.reports.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Stimulsoft Report/2021.3.6/scripts/stimulsoft.viewer.js') }}" type="text/javascript"></script>
    <script src="{{ asset('Stimulsoft Report/2021.3.6/scripts/stimulsoft.dashboards.js') }}"></script>
    <script src="{{ asset('Stimulsoft Report/2021.3.6/scripts/stimulsoft.designer.js') }}" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>

    function onLoad() {

        Stimulsoft.Base.StiLicense.loadFromFile("{{ asset('Stimulsoft Report/2021.3.6/stimulsoft/license.php') }}");
        var viewerOptions = new Stimulsoft.Viewer.StiViewerOptions();

        var viewer = new Stimulsoft.Viewer.StiViewer(viewerOptions, "StiViewer", false);
        var report = new Stimulsoft.Report.StiReport();

        var options = new Stimulsoft.Designer.StiDesignerOptions();
        options.appearance.fullScreenMode = true;

        var designer = new Stimulsoft.Designer.StiDesigner(options, "Designer", false);
        var dataSet = new Stimulsoft.System.Data.DataSet("tes");
        
        viewer.renderHtml("viewerContent");
        report.loadFile("{{ asset('Stimulsoft Report/2021.3.6/reports/reportMasterDetailLaravel.mrt') }}");

        report.dictionary.dataSources.clear();

        dataSet.readJson(<?= $mrtData; ?>);
      
        report.regData("tes", "tes", dataSet);
        report.dictionary.synchronize();
        // var dataRelation = new Stimulsoft.Report.Dictionary.StiDataRelation("contoh", "contoh", "contoh", report.dictionary.dataSources.getByName("customer"), report.dictionary.dataSources.getByName("customer_relations"), ["no_faktur"],  ["no_faktur"]);

      //       report.dictionary.relations.add(dataRelation);
      viewer.report = report;

      // designer.renderHtml("viewerContent");
      // designer.report = report;

    }
    </script>
  </head>
 <body onload="onLoad()">

  <div id="viewerContent"></div>


</body>
</html>