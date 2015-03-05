<div class="logoutdetails-content">
    <table class="tablesorter report" id="sortable_table">
        <thead>
            <tr>
                <?php foreach ($headers as $header) { ?>
                <th><?php echo $header; ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) { ?>
            <tr>
                <?php foreach ($row as $cell) { ?>
                <td><?php echo $cell; ?></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="summary_col">
        <?php foreach($summary_data as $name=>$value) {
            if($name != 'profit'){
        ?>
        <div class="summary_row"><?php echo $this->lang->line('reports_'.$name). ': '.to_currency($value); ?></div>
        <?php }
        } ?>
    </div><br>
    <a href=""></a>

    <?php echo anchor("home/logout/".$fastUser, '<div class="big_button" style="margin:0 auto;"><span>'.$this->lang->line("common_logout").$this->lang->line('recvs_complete_receiving').'</span></div>', '$id="realLogOut"'); ?>
</div>
