<html>
    <head>
        <title>Departments</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        <table border=1px id="userTable">
            <tr>
                <th>Διεύθυνση</th>
                <th>Αναγνωριστικό</th>
                <th>Τμήματα</th>
                <!-- <th>Κατηγορίες</th> -->
            </tr>
            <tbody></tbody>
        </table>

        <script>
            $(document).ready(function(){
                $.ajax({
                    url:'https://coding-factory-php.herokuapp.com/department/list',
                    type:'get',
                    datatype:'JSON'
                })
                .done(function(response){
                    console.log("XXXXX>",response);
                    var len = response.length;
                    for (var i=0; i<len; i++) {
                        var name = response[i].name;
                        var identifier = response[i].identifier;

                        var subdepartment = [];
                        $.each(response[i].subdepartment, function(index,value){
                            subdepartment.push(value.name);
                        });

                        subdepartment = subdepartment.join(',');


                        // console.log(subdepartment);
                        // console.log(name,identifier);

                        var tr_str = "<tr>" + 
                            "<td>" + name + "</td>" +
                            "<td>" + identifier + "</td>" +
                            "<td>" + subdepartment + "</td>" +
                            "</tr>";

                        $("#userTable tbody").append(tr_str);
                    }
                });

                
            });
        </script>
    </body>
</html>
