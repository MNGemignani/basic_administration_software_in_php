<br><br><br><br><br><br><br><br><br><br><br><br>

<!--footer-->
<div class="row">
    <footer>
        <h3>Gemaakt door Mateus &copy; 2017 - <?php echo date("Y"); ?></h3>
    </footer>
</div><!--eind row-->

</div>

<script src="../js/vendor/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js" type="text/javascript" type="text/javascript"></script>
<script src="../js/main.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script>
    //calls the function from the dataTable with the id from the table
    $(document).ready(function(){
        $('#employee_data').DataTable();
    });
    $(document).ready(function(){
        $('#employee_data_2').DataTable();
    });

    function get_child_options(){
        var user_id = $('#user').val();
        $.ajax({
            url: '/simple_admin/admin/parsa.php',
            type: 'POST',
            data: {user_id : user_id},
        }).done(function(data) {
            $('#plan').html(data);
        }).fail(function() {
            console.log('deu merda');
        });
    }
    jQuery('select[name="user"]').change(function () {
        get_child_options();
    });
</script>

</body>
</html>