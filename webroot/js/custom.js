//            var date = new Date();
//            var currentDate = date.toISOString().slice(0,10);
//            document.getElementById('date').value = currentDate;
            
            function getdetails(str)
            {
                var crr=document.getElementById('crr').value;
                var ten=document.getElementById('tenure_code').value;
                var div=document.getElementById('division_code').value;
                if(str.length==0)
                {
                    document.getElementById("name").value="";
                    document.getElementById("address").value="";
                    return;
                }
                else
                {
                    var xmlhttp=new XMLHttpRequest();
                    xmlhttp.onreadystatechange=function()
                    {
                        if(this.readyState == 4 && this.status==200)
                        {
                            var myObj=JSON.parse(this.responseText);
                            document.getElementById("name").value=myObj[0];
                            document.getElementById("address").value=myObj[1];
                            document.getElementById("cs").value=myObj[2];
                            document.getElementById("area").value=myObj[3];
                        }
                        else
                        {
                            document.getElementById("name").value="";
                            document.getElementById("address").value="";
                            document.getElementById("cs").value="";
                            document.getElementById("area").value="";
                        }
                    }
//                    console.log("/payment/add?tenure_code="+ten+ "& division_code=" +div+ "& crr=" +crr);
//                    xmlhttp.open("get","/payment/add?tenure_code="+ten+ "& division_code=" +div+ "& crr=" +crr,true);
                    xmlhttp.send();
                }
            }
            function showdetails(str)
            {
                var crr=document.getElementById('crr').value;
                var ten=document.getElementById('tenure_code').value;
                var div=document.getElementById('division_code').value;
                var data="tenure_code="+ten+"&division_code="+div+"&crr="+crr;
                $.ajax({
                    url:"db/search1.php",
                    method:"GET",
                    data:data,
                    success:function(data){
                        $('#table').html(data);
                    }
                });
                console.log(data);
            }
            function docal()
            {
                var crr=document.getElementById('crr').value;
                var ten=document.getElementById('tenure_code').value;
                var div=document.getElementById('division_code').value;
                var tpa=document.getElementById('tpa').value;
                var total_due=document.getElementById('total_due').value;
                var other_amt=document.getElementById('other').value;
                var data="tenure_code="+ten+"&division_code="+div+"&crr="+crr+"&tpa="+tpa+"&total_due="+total_due+"&other_amt="+other_amt;
                $.ajax({
                    url:"db/search3.php",
                    method:"GET",
                    data:data,
                    success:function(data){
                        $('#table').html(data);
                    }
                });
                console.log(data);
            }
            function docal1()
            {
                var crr=document.getElementById('crr').value;
                var ten=document.getElementById('tenure_code').value;
                var div=document.getElementById('division_code').value;
                var tpa=document.getElementById('tpa').value;
                var total_due=document.getElementById('total_due').value;
                var other_amt=document.getElementById('other').value;
                var data="tenure_code="+ten+"&division_code="+div+"&crr="+crr+"&tpa="+tpa+"&total_due="+total_due+"&other_amt="+other_amt;
                $.ajax({
                    url:"db/search2.php",
                    method:"GET",
                    data:data,
                    success:function(data){
                        $('#table1').html(data);
                    }
                });
                console.log(data);
            }
            function validate()
            {

                var cek = /^[0-9\ \']+$/;
                var name = document.getElementById('crr').value;
                var jnk=document.getElementById('tpa').value;

                if (!cek.test(name) || document.getElementById('crr').value=="")
                {
                document.getElementById('er').innerHTML="Please Enter Valid CRR Numbers Only";

                }
                else
                {
                document.getElementById('er').innerHTML="";  

                }
                if (!cek.test(jnk) || document.getElementById('tpa').value=="")
                {
                document.getElementById('erone').innerHTML="Please Enter Amount";

                }
                else
                {
                document.getElementById('erone').innerHTML="";  

                }

            }
