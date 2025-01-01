@if(!is_null($pagedGroupedData) && count($pagedGroupedData) > 0)
    @php
        $record_id = $pagination['offset'];
    @endphp
    <table class="table table-hover">
        <thead>
            <tr>
                <th width="10px">
                    <input type="checkbox" name="row_check_all" class="row_check_all">
                </th>
                <th>ID</th>
                <th>App User Name</th>
                <th>Date</th>
                <th>Benchpress</th>
                <th>Deadlift</th>
                <th>PowerClean</th>
                <th>Squats</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagedGroupedData as $key => $v)
                <tr>
                    <td>
                        <input type="checkbox" name="row_checkbox[]" class="row_checkbox" value="{{ $v['app_user_id'] }}" data-id="{{ $v['app_user_id'] }}" data-date="{{ $v['date'] }}">
                    </td>
                    <td>{{ ++$record_id }}</td>
                    <td>{{ $v['name'] }}</td>
                    <td>{{ $v['date'] }}</td>
                    <td>{{ $v['benchpress'] }}</td>
                    <td>{{ $v['deadlift'] }}</td>
                    <td>{{ $v['power_clean'] }}</td>
                    <td>{{ $v['squat'] }}</td>
                    <td>{{ Date('d M, Y', strtotime($v['created_at'])) }}</td>
                    <td>
                        @if($v['deleted_at'] == null)
                            <a href="#" data-id="{{ $v['app_user_id'] }}" data-date="{{ $v['date'] }}" class="btn btn-danger btn-sm trash_btn delete{{ $v['app_user_id'] }}">Delete</a>
                        @else
                            <a href="#" data-id="{{ $v['app_user_id'] }}" class="btn btn-primary restore_btn restore{{ $v['app_user_id'] }} btn-sm">Restore</a>
                            <a href="#" data-id="{{ $v['app_user_id'] }}" class="btn btn-danger delete_btn delete{{ $v['app_user_id'] }} btn-sm">Delete</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-warning" align="center">
        Opps, seems like records not available.
    </div>
@endif

@if($pagination['total_records'] > $pagination['item_per_page'])
<div class="card-header">
  <div class="pl-3">
    <div class="paging_simple_numbers">
      <ul class="pagination">
        <?php
        echo paginate_function($pagination['item_per_page'], $pagination['current_page'], $pagination['total_records'], $pagination['total_pages']);
        ?>
      </ul>
    </div>
  </div>
</div>
@endif

<?php
function paginate_function($item_per_page, $current_page, $total_records, $total_pages) {
  $pagination = '';
  if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) { //verify total pages and current page number
    $right_links  = $current_page + 3;
    $previous    = $current_page - 3; //previous link
    $next     = $current_page + 1; //next link
    $first_link  = true; //boolean var to decide our first link

    if ($current_page > 1) {
      $previous_link = ($previous <= 0) ? 1 : $previous;
      $pagination .= '<li class="page-item "><a class="paginate_link page-link"  href="#" aria-controls="datatable1" data-page="1" title="First">&laquo;</a></li>'; //first link
      $pagination .= '<li class="page-item "><a class="paginate_link page-link"  href="#" aria-controls="datatable1" data-page="' . $previous_link . '" title="Previous">&lt;</a></li>'; //previous link
      for ($i = ($current_page - 2); $i < $current_page; $i++) { //Create left-hand side links
        if ($i > 0) {
          $pagination .= '<li class="page-item "><a class="paginate_link page-link"  href="#" data-page="' . $i . '" aria-controls="datatable1" title="Page' . $i . '">' . $i . '</a></li>';
        }
      }
      $first_link = false; //set first link to false
    }

    if ($first_link) { //if current active page is first link
      $pagination .= '<li class="page-item active">
        <a class="paginate_link page-link" aria-controls="datatable1">' . $current_page . '</a></li>';
    } elseif ($current_page == $total_pages) { //if it's the last active link
      $pagination .= '<li class="page-item active">
        <a class="paginate_link page-link" aria-controls="datatable1">' . $current_page . '</a></li>';
    } else { //regular current link
      $pagination .= '<li class="page-item active">
        <a class="paginate_link page-link" aria-controls="datatable1">' . $current_page . '</a></li>';
    }

    for ($i = $current_page + 1; $i < $right_links; $i++) { //create right-hand side links
      if ($i <= $total_pages) {
        $pagination .= '<li class="page-item "><a class="paginate_link page-link" href="#" aria-controls="datatable1" data-page="' . $i . '" title="Page ' . $i . '">' . $i . '</a></li>';
      }
    }
    if ($current_page < $total_pages) {
      $next_link = ($i > $total_pages) ? $total_pages : $i;
      $pagination .= '<li class="page-item "><a class="paginate_link page-link" href="#" aria-controls="datatable1" data-page="' . $next_link . '" title="Next">&gt;</a></li>'; //next link
      $pagination .= '<li class="page-item "><a class="paginate_link page-link" href="#" aria-controls="datatable1" data-page="' . $total_pages . '" title="Last">&raquo;</a></li>'; //last link
    }
  }
  return $pagination; //return pagination links
}
?>
