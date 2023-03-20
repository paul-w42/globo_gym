/* Admin Dashboard Script */
google.charts.load('current', {
    packages: ['corechart']
}).then(function() {
    var memberButton = document.getElementById('b1');
    var visitButton = document.getElementById('b2');
    var revenueButton = document.getElementById('b3');
    var newMemberButton = document.getElementById('b4');



    // switch to total members chart when clicked
    memberButton.onclick = function() {
        drawMemberChart('LineChart');
    }

    // switch to total new members chart when clicked
    newMemberButton.onclick = function() {
        drawNewMemberChart('ColumnChart');
    }

    // switch to total visits chart when clicked
    visitButton.onclick = function() {
        drawVisitsChart('LineChart');
    }

    // switch to total revenue chart when clicked
    revenueButton.onclick = function() {
        drawRevenueChart('LineChart');
    }

    // default/active chart
    drawMemberChart('LineChart');
});

// code for member chart
function drawMemberChart(chartType) {
    var chart = new google.visualization.ChartWrapper({
        containerId: 'globogym_chart'
    });

// chart data -- TODO: need to add database monthly TOTAL MEMBERS data here (put database data in the number, not the month)
    var data = newMembersMonth

    // chart title/headers and styling
    var options = {
        // title
        title: 'Total Members (last 12 months)',
        titleTextStyle: {
            fontName: 'Arial',
            fontSize: 20,
            bold: true,
            italic: false,
        },
        // x axis
        hAxis: {
            title: 'Month',
            titleTextStyle: {
                italic: false,
            },
            direction:1,
            slantedText: true,
            slantedTextAngle:50,
        },
        // y axis
        vAxis: {
            title: 'Members',
            titleTextStyle: {
                italic: false,
            },
        },
        // line
        series: {
            0: {
                color: '#663399',
            },
        },
        legend: 'none',
    };
    chart.setOptions(options);
    chart.setChartType(chartType);
    chart.setDataTable(data);
    chart.draw();

    window.addEventListener('resize', function() {
        chart.draw()
    }, false);
}

// code for new members chart
function drawNewMemberChart(chartType) {
    var chart = new google.visualization.ChartWrapper({
        containerId: 'globogym_chart'
    });

    // chart data -- TODO: need to add database monthly TOTAL MEMBERS data here (put database data in the number, not the month)
    var data = totalMembersData;

    // chart title/headers and styling
    var options = {
        // title
        title: 'Total New Members (last 12 months)',
        titleTextStyle: {
            fontName: 'Arial',
            fontSize: 20,
            bold: true,
            italic: false,
        },
        // x axis
        hAxis: {
            title: 'Month',
            titleTextStyle: {
                italic: false,
            },
            direction:1,
            slantedText:true,
            slantedTextAngle:50,
        },
        // y axis
        vAxis: {
            title: 'New Members',
            titleTextStyle: {
                italic: false,
            },
        },
        // line
        series: {
            0: {
                color: '#663399',
            },
        },
        legend: 'none',
    };
    chart.setOptions(options);
    chart.setChartType(chartType);
    chart.setDataTable(data);
    chart.draw();

    window.addEventListener('resize', function() {
        chart.draw()
    }, false);
}

// code for visits chart
function drawVisitsChart(chartType) {
    var chart = new google.visualization.ChartWrapper({
        containerId: 'globogym_chart'
    });

    // chart data -- TODO: need to add database monthly TOTAL VISITS data here (put database data in the number, not the month)
    var data = google.visualization.arrayToDataTable([
        ['Month', 'Visits'],
        ['Mar',7],['Apr',8],['May',8],['Jun',9],['Jul',9],
        ['Aug',9],['Sept',10],['Oct',11],
        ['Nov',14],['Dec',14],['Jan',9],['Feb',10]
    ]);

    // chart title/headers and styling
    var options = {
        // title
        title: 'Total Visits (last 12 months)',
        titleTextStyle: {
            fontName: 'Arial',
            fontSize: 20,
            bold: true,
            italic: false,
        },
        // x axis
        hAxis: {
            title: 'Month',
            titleTextStyle: {
                italic: false,
            },
            direction:1,
            slantedText:true,
            slantedTextAngle:50,
        },
        // y axis
        vAxis: {
            title: 'Visits',
            titleTextStyle: {
                italic: false,
            },
        },
        // line
        series: {
            0: {
                color: '#663399',
            },
        },
        legend: 'none',
    };
    chart.setOptions(options);
    chart.setChartType(chartType);
    chart.setDataTable(data);
    chart.draw();

    window.addEventListener('resize', function() {
        chart.draw()
    }, false);
}

// code for revenue chart
function drawRevenueChart(chartType) {
    var chart = new google.visualization.ChartWrapper({
        containerId: 'globogym_chart'
    });

    // chart data -- TODO: need to add database monthly TOTAL REVENUE data here (put database data in the number, not the month)
    var data = google.visualization.arrayToDataTable([
        ['Month', 'Revenue'],
        ['Mar',4],['Apr',5],['May',7],['Jun',7],['Jul',7],
        ['Aug',6],['Sept',8],['Oct',8],
        ['Nov',9],['Dec',10],['Jan',11],['Feb',12]
    ]);

    // chart title/headers and styling
    var options = {
        // title
        title: 'Total Revenue (last 12 months)',
        titleTextStyle: {
            fontName: 'Arial',
            fontSize: 20,
            bold: true,
            italic: false,
        },
        // x axis
        hAxis: {
            title: 'Month',
            titleTextStyle: {
                italic: false,
            },
            direction:1,
            slantedText:true,
            slantedTextAngle:50,
        },
        // y axis
        vAxis: {
            title: 'Revenue',
            titleTextStyle: {
                italic: false,
            },
        },
        // line
        series: {
            0: {
                color: '#663399',
            },
        },
        legend: 'none',
    };
    chart.setOptions(options);
    chart.setChartType(chartType);
    chart.setDataTable(data);
    chart.draw();

    window.addEventListener('resize', function() {
        chart.draw()
    }, false);
}

/* Join Page Scripts */
// auto-formatting phone number - step 1
function formatPhoneNumber1() {
    // retrieve value of what user is typing and get by element ID
    let inputField = document.getElementById('phone');

    // format input with the `formatPhoneNumber2` function.
    let inputValue = formatPhoneNumber2(inputField.value);

    // set the value of the inputField to the inputValue
    inputField.value = inputValue;
}

// auto-formatting phone number - step 2
function formatPhoneNumber2(value) {
    // if input value is false, then just return
    if (!value) return value;

    // clean input for non-digit values
    let phoneNumber = value.replace(/[^\d]/g, '');

    // set phone number length
    let phoneNumberLength = phoneNumber.length;

    // return the value with no formatting if its less than four digits
    if (phoneNumberLength < 4) return phoneNumber;

    // if phoneNumberLength is greater than 4 and less the 7, start typing formatted number
    if (phoneNumberLength < 7) {
        return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(3)}`;
    }

    // if the phoneNumberLength is greater than seven, we add the last
    // bit of formatting and return it.
    return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(
        3,
        6
    )}-${phoneNumber.slice(6, 9)}`;
}
