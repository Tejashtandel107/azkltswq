<div class="row">                                  
    <div class="col-lg-12 col-md-12 col-sm-12">
        @if(isset($results) && $results->count()>0)
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr class="table-success">
                        <th>Vakkal</th>
                        <th>Item</th>
                        <th>Marka</th>
                        <th>Details</th>
                        <th>Balance</th>
                        <th>Customer</th>
                        <th>Location Code</th>
                        <th>Add</th>
                    </tr>
                </thead>
                <tbody>        
                    @foreach($results as $result)
                    <tr>
                        <td>{{ $result->vakkal_number }}</td>
                        <td>{{ $result->item_name }}</td>
                        <td>{{ $result->marka_name }}</td>
                        <td>{{ $result->description }}</td>
                        <td>{{ $result->balance_quantity}}</td>
                        <td>{{ $result->companyname }}</td>
                        <td>{{ Helper::getLocationCode(($result->chamber_number) ?? ' -- ',($result->floor_number) ?? ' -- ',($result->grid_number) ?? ' --') }}</td>
                        <td><button type="button" class="btn btn-blue btn-sm" onclick="getInwardData({{ $result->order_item_id }})">Add</button></td>
                    </tr>
                    @endforeach                
                </tbody>    
            </table>
        </div>
        @else                
        <div class="alert alert-danger has-icon"><i class="la la-warning alert-icon"></i>No records found. </div>
        @endif
    </div>
</div>

<script type = "text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<script type="text/javascript">
function getInwardData(order_item_id) {
    if(order_item_id>0){
        var order_date = $('#order-date').val();
        ajaxFetch('/api/inwards/getInward',{'order_item_id':order_item_id,'outward_order_date':order_date},function(data,textStatus){
            if(textStatus=="success"){          
                $('#adddivision').append(data);
                $('html, body').animate({ 'scrollTop' : $(".display-group:last-child").position().top }, 2000);
                hideLoader();
                $('#adddivision .display-group:last-child').effect( "highlight", {color:"#c3e6cb"}, 3000 );
            }
        },'',"POST",true);
    }
}
</script>