google.charts.load("current", {packages:["corechart","gauge"]});
google.charts.setOnLoadCallback(drawChart);

var gaugeData;  var gaugeChart; var gaugeOption;
var projectsData;  var projectsChart; var projectsOption;
var filesData;  var filesChart; var filesOption;
var fileData;   var fileChart;  var fileOption;

var projectOk=0; var projectError=0;
var fileOk=0; var fileError=0;
var testOk=0; var testError=0;
var assertOk=0; var assertError=0;

var aktProjectNr=0; var aktFileNr=0; var aktTestNr=0; var aktFileError=false;
var timeSumm = 0; var timeCount = 0;

var testProjects = new Array();
var testFiles;

$(window).on("resize", function (event) {
    projectsChart.draw(projectsData, projectsOption);
    filesChart.draw(filesData, filesOption);
    fileChart.draw(fileData, fileOption)
});

function drawChart() {
    drawGaugeChart();
    drawProjectsChart();
    drawFilesChart();
    drawFileChart();
    getTestFiles();
}

function drawGaugeChart() {
    gaugeData = google.visualization.arrayToDataTable([['Label', 'Value'], ['Speed', 0]]);
    gaugeOption = {
        width: 120, height: 100,
        greenFrom: 50, greenTo: 100,
        redFrom: 0, redTo: 10,
        yellowFrom: 10, yellowTo: 35,
        minorTicks: 5
    };
    gaugeChart = new google.visualization.Gauge(document.getElementById('speedGauge'));
    gaugeChart.draw(gaugeData, gaugeOption);
}

function drawProjectsChart() {
    projectsData = google.visualization.arrayToDataTable([['Task', 'Files'], ['', 1]]);
    projectsOption = {
        title: 'PhpUnit projects:',
        pieHole: 0.4,
        colors: ['gray'],
        chartArea: {left: 0, top: 20, width: '100%', height: '100%'},
    };
    projectsChart = new google.visualization.PieChart(document.getElementById('projectsGauge'));
    google.visualization.events.addListener(projectsChart, 'click', selectProjectHandler);
    projectsChart.draw(projectsData, projectsOption);

    function selectProjectHandler() {
        var selectedItem = projectsChart.getSelection()[0];
        if (selectedItem) {
            aktTestNr = 0;
            resetCounters();
            $("#console").empty();
            runTest(selectedItem.row);
        }
    }
}

function drawFilesChart() {
    filesData = google.visualization.arrayToDataTable([['Task', 'Files'], ['', 1]]);
    filesOption = {
        title: 'PhpUnit files:',
        pieHole: 0.4,
        colors: ['gray'],
        chartArea: {left: 0, top: 20, width: '100%', height: '100%'},
    };
    filesChart = new google.visualization.PieChart(document.getElementById('filesGauge'));
    google.visualization.events.addListener(filesChart, 'click', selectFilesHandler);
    filesChart.draw(filesData, filesOption);
    function selectFilesHandler() {
        var selectedItem = filesChart.getSelection()[0];
        if (selectedItem) {
            aktTestNr = 0;
            resetCounters();
            $("#console").empty();
            runTest(testFiles[selectedItem.row].name, selectedItem.row);
        }
    }

}

function drawFileChart() {
    fileData = google.visualization.arrayToDataTable([['Task','Tests' ],['',1]]);
    fileOption = {
        title: 'Test file:',
        allowHtml:true,
        pieHole: 0.4,
        colors: ['gray'],
        chartArea:{left:0,top:20,width:'100%',height:'100%'}
    };
    fileChart = new google.visualization.PieChart(document.getElementById('fileGauge'));
    google.visualization.events.addListener(fileChart, 'click', selectFileHandler);
    fileChart.draw(fileData, fileOption);
    function selectFileHandler() {
        var selectedItem = fileChart.getSelection()[0];
        if (selectedItem) {
            resetCounters();$("#console").empty();
            runTest(testFiles[aktFileNr].name,selectedItem.row);
        }
    }
}

function getTestFiles() {
    $.ajax({
        url:'ajaxGetTestFiles.php',
        type:'GET',
        success:function(data){
            testFiles=data;
            while (filesData.getNumberOfRows()>0)
                filesData.removeRow(0);
            while (projectsData.getNumberOfRows()>0)
                projectsData.removeRow(0);
            var i=0;
            var tests=0;
            var projects=0;
            var projectsTests=0;
            var changeProject="";
            var asserts=0;
            data.forEach(function(testFile) {
                filesOption.colors[i++]='blue';
                filesData.addRow([testFile.name+' '+testFile.file,testFile.tests]);
                tests+=testFile.tests;
                asserts+=testFile.asserts;
                if( changeProject!==testFile.name) {
                    testProjects.push(testFile.name);
                    if (projectsTests!=0)
                        projectsData.addRow([changeProject,projectsTests]);
                    projectsOption.colors[projects++]='blue';
                    changeProject=testFile.name;
                    projectsTests=0;
                }
                projectsTests+=testFile.tests;
            });
            projectsData.addRow([data[data.length-1].name,projectsTests]);
            filesOption.title ='PhpUnit files:'+filesData.getNumberOfRows()+' tests:'+tests+" asserts:"+asserts;
            filesChart.draw(filesData, filesOption );
            projectsOption.title ='PhpUnit projects:'+projects;
            projectsChart.draw(projectsData, projectsOption );
            //ToDo ower a variable if (settings.autoRun) runAlltests();

        },
        error:function() {
            alert('Error getting the list of test files!');
        }
    });
}

function resetCounters() {
    projectOk=0; projectError=0;
    fileOk=0; fileError=0;
    testOk=0; testError=0;
    assertOk=0; assertError=0;
    timeCount = 0; timeSumm=0;
}

function runAlltests() {
    aktProjectNr=0;aktFileNr=0;aktTestNr=0;aktFileError=false;
    resetCounters();
    for(var i=0;i<projectsData.getNumberOfRows();i++) {
        projectsOption.colors[i] = 'blue';
    }
    for(var i=0;i<filesData.getNumberOfRows();i++) {
        filesOption.colors[i] = 'blue';
    }
    setSpeedGauge();
    $("#console").empty();
    runTest();
}

/**
 * Run Ajax Text
 * @param oneProjectNr if set then only one project is to be tested
 * @param oneFileNr if set then only one file is to be tested
 * @param oneTestNr if set then only one test ist to be tested
 */
function runTest(oneProjectNr,oneFileNr,oneTestNr) {
    if (oneProjectNr!=null) aktProjectNr=oneProjectNr;
    if (oneFileNr!=null) aktFileNr=oneFileNr;
    if (oneTestNr!=null) aktTestNr=oneTestNr;
    console.log("ProjectNr:"+aktProjectNr+" FileNr:"+aktFileNr+" TestNr:"+aktTestNr);

    var testProject=testProjects[aktProjectNr];
    var testFile=testFiles[aktFileNr];
    if (aktTestNr===0) {
        aktFileError=false;
        //Display testfile name
        fileOption.title ='Test file:'+testFiles[aktFileNr].file;
        //Delete file tests from pie diagram and add only one blue element
        while (fileData.getNumberOfRows()>0)
            fileData.removeRow(0);
        fileData.addRow(["",1]);
        fileOption.colors[0]="blue";
        fileChart.draw(fileData, fileOption);
    }
    aktProjectNr=testProjects.indexOf(testFile.name);
    projectsOption.colors[aktProjectNr]="orange";
    projectsChart.draw(projectsData, projectsOption );
    $.ajax({
        url:'ajaxUnitTestRun.php?file='+testFile.file+"&dir="+testFile.dir+"&testNr="+aktTestNr,
        type:'GET',
        success:function(data){
            if (typeof(data.filestatus)==null || data.filestatus === "error") {
                setTextToConsole(data.errorMessage, 'red', true);
                filesOption.colors[aktFileNr] = 'red';
                aktTestNr = 0;
                aktFileNr++;
                fileError++;
                if (aktFileNr < filesData.getNumberOfRows() && oneFileNr == null) {
                    runTest(oneProjectNr,oneFileNr, oneTestNr);
                }
            } else {
                if (aktTestNr === 0 || oneTestNr != null) {
                    fileOption.title = 'Test file:' + testFiles[aktFileNr].file + '\nTest name:';
                    //Initialise the tests pie with blue elements
                    while (fileData.getNumberOfRows() > 0)
                        fileData.removeRow(0);
                    for (var i = 0; i < data.tests.length; i++) {
                        fileData.addRow([data.tests[i], 1]);
                        fileOption.colors[i] = "blue";
                    }
                }

                if (data.filestatus === "done") {
                    setTestResults(data);
                    if (!aktFileError) {
                        filesOption.colors[aktFileNr] = 'green';
                        fileOk++;
                    } else {
                        filesOption.colors[aktFileNr] = 'red';
                        fileError++;
                    }
                    filesChart.draw(filesData, filesOption );
                    aktTestNr = 0;
                    if (oneTestNr == null && oneFileNr == null) {
                        if (aktFileNr+1 < filesData.getNumberOfRows()) {
                            aktFileNr++;
                            runTest(oneProjectNr,oneFileNr, oneTestNr);
                        }
                    }

                }
                if (data.filestatus === "running") {

                    filesOption.colors[aktFileNr] = 'yellow';
                    filesChart.draw(filesData, filesOption );
                    setTestResults(data);
                    aktTestNr++;
                    if (oneTestNr == null)
                        runTest(oneProjectNr,oneFileNr, oneTestNr);
                }
            }
        },
        error:function(error) {
            setTextToConsole(error, 'red', true);
            filesOption.colors[aktFileNr] = 'red';
            fileError++;
            filesChart.draw(filesData, filesOption );
            aktTestNr = 0;
            aktFileNr++;
            if (aktFileNr < filesData.getNumberOfRows() && oneFileNr == null) {
                runTest(oneProjectNr,oneFileNr, oneTestNr);
            }
        }
    });
}

function setTestResults(data) {
    fileOption.title = 'Test file:' + testFiles[aktFileNr].file;
    if (data.test === true) {
        if (data.assertOk>0) {
            $color = 'green';
        } else {
            $color = 'orange';
        }
        testOk++;
        assertOk += data.assertOk;
    } else {
        $color = 'red';
        testError++;
        aktFileError=true;
        assertOk += data.assertOk;
        assertError += data.assertError;
    }
    fileOption.colors[aktTestNr] = $color;
    fileChart.draw(fileData, fileOption);

    setTextToConsole(data.time + 'ms ' + data.testName, $color, true);
    timeSumm +=parseFloat(data.time);
    timeCount ++;
    setSpeedGauge();



    if (data.echo.length>0) {
        setTextToConsole(data.echo,$color);
    }
    if (data.errorMessage!==null) {
        setTextToConsole(data.errorMessage,"red",true);
    }
    showResultCounters();
}

function setTextToConsole(text,color,newline) {
    if (color == null) color = "black";
    if (newline == null || newline === false)
        newline = "span";
    else
        newline = "div";
    var c= $('<'+newline+' style="color:'+color+'"></'+newline+'>');
    c.html(text);
    $("#console").append(c);
}

function setSpeedGauge() {
    if (timeSumm>0) {
        var speed = timeCount/(timeSumm/1000);
    } else {
        var speed = 0;
    }
    gaugeData.setValue(0, 1, speed.toFixed(1));
    gaugeChart.draw(gaugeData, gaugeOption);
}

function showResultCounters() {
    $('#pok').text(projectOk);   $('#perror').text(projectError);
    $('#fok').text(fileOk);   $('#ferror').text(fileError);
    $('#tok').text(testOk);   $('#terror').text(testError);
    $('#aok').text(assertOk); $('#aerror').text(assertError);
}
