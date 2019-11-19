 <!DOCTYPE html>
<html>
<head>
    <title></title>

    <?php include('meta.php');?>
    <?php include('style_css.php');?>
</head>
<body>

<?php  
include("db.php");
    $survey_sql = mysqli_query($con,"SELECT s.survey_id, 
       s.survey_name, 
       s.survey_date, 
       sq.survey_qid, 
       sq.question, 
       sao.survey_aid, 
       sao.answer, 
       (SELECT IF(sao.answer = 'other(s)' 
                   OR sao.answer = 'others' 
                   OR sao.answer = 'other', 
               Coalesce((SELECT 
               Count(sa.survey_aid) 
                         FROM   `survey_answer_other` sa 
                         WHERE 
               sa.survey_aid = sao.survey_aid 
                         GROUP  BY sa.survey_aid), 0), 
                       Coalesce((SELECT Count(sa.survey_aid) 
                                 FROM   `survey_answer` sa 
                                 WHERE  sa.survey_aid = sao.survey_aid 
                                 GROUP  BY sa.survey_aid), 0))) count_answer, 
       Concat('y:', (SELECT IF(sao.answer = 'other(s)' 
                                OR sao.answer = 'others' 
                                OR sao.answer = 'other', Coalesce( 
                            (SELECT Count(sa.survey_aid) 
                             FROM   `survey_answer_other` sa 
                             WHERE 
                            sa.survey_aid = sao.survey_aid 
                                      GROUP  BY 
                            sa.survey_aid), 0), 
                                                 Coalesce( 
                            (SELECT Count(sa.survey_aid) 
                             FROM   `survey_answer` sa 
                             WHERE 
                                                 sa.survey_aid = sao.survey_aid 
                                                           GROUP  BY 
                            sa.survey_aid), 0))), ', label:', sao.answer) 
                                                                diag_data_label 
FROM   survey_anweroptions sao 
       LEFT JOIN survey_questionnaire sq 
              ON sq.survey_qid = sao.survey_qid 
       LEFT JOIN survey s 
              ON s.survey_id = sq.survey_id 
WHERE  s.visibility = 1 
ORDER  BY sq.survey_qid ");
    $z = array();
    $zcx = array();
    $data_label = array();
    $data_value = array();
    while ($survey = mysqli_fetch_array($survey_sql))
    {
            $z[] = $survey['diag_data_label'];
            $piece = explode(",", $survey['diag_data_label']);
            $part = $piece[0];
            
            $data = explode(":", $part);
            $data_label[] = $data[0];
            $data_value[] = $data[1];
          
    }
    // echo        $data_label =  json_encode($data_label);
    // echo        $data_value =  json_encode($data_value);
        
         foreach ($z as $key => $value) {
          $zcx[] =  "{".$value."}";
          
         }
         
   $jencode_title =  json_encode($zcx);
  $jencode_title = str_replace("\"","",$jencode_title);
   $jencode_title = str_replace("}","\"}",$jencode_title);
   $jencode_title = str_replace("label:","label:\"",$jencode_title);
?>
 <center>
<h1>asdasd</h1>

</center>
<hr>
<form class="form-inline" method='get'>
  <div class="form-group">
       
        <select name="category" id="category" onchange="showCategory(this.value)">
        <option value="ACCOUNT REGISTER" >ACCOUNT REGISTER</option>
        <option value="ACCOUNT UNREGISTER" >ACCOUNT UNREGISTER</option>
        <option value="CATEGORY 1">CATEGORY 1</option>
        <option value="CATEGORY 2">CATEGORY 2</option>
        <option value="CATEGORY 3">CATEGORY 3</option>
        </select>
    </div>
    <div class="form-group">
        <input class="form-control" id="monthx" type="month" name="monthx" onchange="filter_datex(this.value)">
    </div>

  <select class="form-control input-sm" id="chartType" name="Chart Type">
    <option value="line">Line</option>
    <option value="column">Column</option>
    <option value="bar">Bar</option>
    <option value="pie">Pie</option>
    <option value="doughnut">Doughnut</option>
  </select>  
    <div class="form-group">
        <a class="btn btn-primary pull-right" onclick="printDoc()"  target="_blank" >PRINT</a>
    </div>
    
</form>



  <div  id="printDiv" >
    <div id="displayChart">
    <script>
     
    window.onload = function() {
     

      var chart = new CanvasJS.Chart("chartContainer", {
      animationEnabled: true,
      title: {
        text: "First Default Chart Before Onchange"
      },
      data: [{
        type: "pie",
        startAngle: 240,
        yValueFormatString: "##0.00'%'",
        indexLabel: "{label} {y}",
        dataPoints: <?php 
      echo $jencode_title;
      ?> 
      
      }]
    });
    chart.render();
    var chartType = document.getElementById('chartType');
    chartType.addEventListener( "change",  function(){
      chart.options.data[0].type = chartType.options[chartType.selectedIndex].value;
      chart.render();
    });

    }
    </script>

  </div>
</div>

<div id="chartContainer" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>
<script src="assets/lib/canvas/canvasjs.min.js"></script>
<div id="thediv"></div>
<script type="text/javascript">
  function getDate(){
    var monthx =document.getElementById('monthx').value;
  
  }
  function getCategory(){
    var category =document.getElementById('category').value;

  }
  function printDoc(){
    var a = getCategory();
    alert(a);
    // window.location='assets/lib/FPDF/print?category='+getCategory+'&date='+getDate;
    // alert('assets/lib/FPDF/print?category='+getCategory+'&date='+getDate);
  }
  $('#monthx').hide();
  function showCategory(str) {
  var xhttp; 
  if (str == "") {
    document.getElementById("displayChart").innerHTML = "";
    return;
  }
  if (str == "ACCOUNT REGISTER" || str == "ACCOUNT UNREGISTER" || str == "default") {
    $('#monthx').hide();
  }
  else{
    $('#monthx').show();
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    

                    var json_datax = this.responseText;
                    var monthx =document.getElementById('monthx').value;
                    var category =document.getElementById('category').value;
                    alert(json_datax);
                    console.log(this.responseText)
                    var chart=new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true, title: {
                            text: ''+category+" "+monthx+''
                        }
                        , data: [ {
                            type: "pie", startAngle: 240, yValueFormatString: "##0.00'%'", indexLabel: "{label} {y}", dataPoints: 
                            [ {
                                y: 79.45, label: 3123
                            }
                            , {
                                y: 7.31, label: "x"
                            }
                            , {
                                y: 7.06, label: "Baidu"
                            }
                            , {
                                y: 4.91, label: "Yahoo"
                            }
                            , {
                                y: 11.26, label: "Others"
                            }
                            ]
                        }
                        ]
                    }
                    );
                    chart.render();
                    var chartType=document.getElementById('chartType');

                    chartType.addEventListener( "change", function() {
                        chart.options.data[0].type=chartType.options[chartType.selectedIndex].value;
                        chart.render();
                    }
                    );
                }
            };
  xhttp.open("GET", "chart_ajax.php?category="+str+"&date="+monthx, true);
  xhttp.send();
}

  function filter_datex(str) {
  var xhttp; 
  if (str == "") {
    document.getElementById("displayChart").innerHTML = "";
    return;
  }
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var json_datax = this.responseText;
                    var monthx =document.getElementById('monthx').value;
                    var category =document.getElementById('category').value;
                    console.log(this.responseText)
                    var chart=new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true, title: {
                            text: ''+category+" "+monthx+''
                        }
                        , data: [ {
                            type: "pie", startAngle: 240, yValueFormatString: "##0.00'%'", indexLabel: "{label} {y}", dataPoints: 
                            [ {
                                y: 79.45, label: 3123
                            }
                            , {
                                y: 7.31, label: "x"
                            }
                            , {
                                y: 7.06, label: "Baidu"
                            }
                            , {
                                y: 4.91, label: "Yahoo"
                            }
                            , {
                                y: 11.26, label: "Others"
                            }
                            ]
                        }
                        ]
                    }
                    );
                    chart.render();
                    var chartType=document.getElementById('chartType');

                    chartType.addEventListener( "change", function() {
                        chart.options.data[0].type=chartType.options[chartType.selectedIndex].value;
                        chart.render();
                    }
                    );
                }
            };
  xhttp.open("GET", "chart_ajax.php?category="+monthx+"&date="+str, true);
  xhttp.send();
  }
 
</script>

            <!-- /#wrap -->
            <?php include('footer.php');?>
            <!-- /#footer -->
            <?php include ('script.php');?>
            <script type="text/javascript">
              // function updateContent(){

              // $("#thediv").load('try2.php'); 
              // }
              // setInterval(function() {updateContent()}, 500);
              
            </script>
            