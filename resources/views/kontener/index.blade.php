<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Laravel Autocomplete Search Box</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="margin-top: 50px;">
    <div class="row">
        <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <h3 class="text-center">Laravel Autocomplete Search Box</h3><hr>
                <div class="form-group">
                    <h4>Type by id, name and email!</h4>
                    <input type="text" name="search" id="search" placeholder="Enter search name" class="form-control" onfocus="this.value=''">
                </div>
                <div id="search_list"></div>
            </div>
        <div class="col-lg-3"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script>
    $(document).ready(function(){
        $('#search').on('keyup',function()
        {
            var query= $(this).val();
            if (query.length>=3) {
                $.ajax({
                    url:"search",
                    type:"GET",
                    data:{'search':query},
                    success:function(data)
                    {
                        $('#search_list').html(data);
                    }
                });
            }
             //end of ajax call
        });
    });
</script>
</body>
</html>