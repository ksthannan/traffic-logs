<?php 
defined( 'ABSPATH' ) or die;
if(isset($_REQUEST['traffic_url'])){
   $request = $_REQUEST['traffic_url'];
   $post_id = url_to_postid($request);
   
    $post_title = get_the_title($post_id);
    
    $logs_data = get_post_meta($post_id, 'traffic_logs', false);
    
?>
<h1><?php _e( 'Logs : '. $post_title, 'traffic-logs' ); ?></h1>
<table class="widefat fixed traffic_logs_table" cellspacing="0">
<thead>
    
<tr>
        <th id="columnname" class="manage-column column-columnname num" scope="col">Serial</th>
        <th id="columnname" class="manage-column column-columnname" scope="col">Page/Post</th>
        <th id="columnname" class="manage-column column-columnname" scope="col">IP Address</th>
        <th id="columnname" class="manage-column column-columnname" scope="col">User</th> 
        <th id="columnname" class="manage-column column-columnname" scope="col">Country</th> 
        <th id="columnname" class="manage-column column-columnname" scope="col">Time</th> 

</tr>
</thead>

<tbody>
<?php 
    
    $i = 0;
    
    $logs = false;
    
    $get_logs = array_reverse($logs_data[0]);

    foreach($get_logs as $log_data){
        
        $i++;
        
        $user_login = $log_data['user'];
        $user = get_user_by('login', $user_login);
        $user_profile_link = get_edit_user_link($user->ID);
        
        $date_time = new DateTime($log_data['time']);
        $formatted_date = $date_time->format('jS M, Y \a\t h:i a');
         
       ?>
        <tr class="alternate">
            <td class="column-columnname num"><?php echo $i;?></td>
            <td class="column-columnname"><a href="<?php echo $request;?>"><?php echo $post_title;?></a></td>
            <td class="column-columnname"><?php echo $log_data['visitor_ip'];?> </td>
            <td class="column-columnname"><?php echo $log_data['user'] ? '<a href="'.$user_profile_link.'">' . $user_login . '</a>' : ' --- ';?> </td>
            <td class="column-columnname"><?php echo $log_data['location']->region;?>, <?php echo $log_data['location']->country;?> </td>
            <td class="column-columnname"><?php echo $formatted_date;?> </td>
        </tr>
    <?php  
    
    $logs = true;
    
    }
    
}else{
   $pages = $this->get_option( 'links', array() );
    ?>
<h1><?php _e( 'Logs', 'traffic-logs' ); ?></h1>
<table class="widefat fixed traffic_logs_table" cellspacing="0">
<thead>
    <tr>
            <th id="columnname" class="manage-column column-columnname num" scope="col">Serial</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">Page/Post</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">IP Address</th>
            <th id="columnname" class="manage-column column-columnname" scope="col">User</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Country</th> 
            <th id="columnname" class="manage-column column-columnname" scope="col">Time</th> 
    
    </tr>
    </thead>
    
    <tbody>
    <?php 
    $i = 0;
    
    
    $get_logs = get_option('tr_recent_logs', array());
       
// echo '<pre>';
// print_r($get_logs);
// echo '</pre>';  

    $get_logs = array_reverse($get_logs);
    
    foreach($get_logs as $get_log){
        
        $post_id = $get_log['page_id'];
        
        $post_title = get_the_title($post_id);
        
            $i++;
            
            
            $user_login = $get_log['user'];
            $user = get_user_by('login', $user_login);
            $user_profile_link = get_edit_user_link($user->ID);
            
            $date_time = new DateTime($get_log['time']);
            $formatted_date = $date_time->format('jS M, Y \a\t h:i a');
            
           ?>
            <tr class="alternate">
                <td class="column-columnname num"><?php echo $i;?></td>
                <td class="column-columnname"><a href="<?php echo get_permalink($post_id);?>"><?php echo $post_title;?></a></td>
                <td class="column-columnname"><?php echo $get_log['visitor_ip'];?> </td>
                <td class="column-columnname"><?php echo $get_log['user'] ? '<a href="'.$user_profile_link.'">' . $user_login . '</a>' : ' --- ';?> </td>
                <td class="column-columnname"><?php echo $get_log['location']->region;?>, <?php echo $get_log['location']->country;?> </td>
                <td class="column-columnname"><?php echo $formatted_date;?> </td>
            </tr>
        <?php 
        
        $logs = true;
        
      
        
    } 
}

if($logs == false){
    ?>
    <tr class="alternate">
        <td>No logs found!</td>
    </tr>
    <?php 
}



?>
<p>Pages Visit : <?php echo $i;?></p>
<a href="<?php echo admin_url('admin.php?page=traffic-logs&clear_traffic_log=yes');?>" class="button button-secondary traffic-logs-content-clear-log"><?php esc_html_e( 'Clear Log', 'traffic-logs-content' ); ?></a> 
<a href="<?php echo admin_url('admin.php?page=traffic-logs-settings');?>" class="button button-primary traffic-logs-content-settings"><?php esc_html_e( 'Settings', 'traffic-logs-content' ); ?></a> 
<p></p>
</tbody>
</table>

