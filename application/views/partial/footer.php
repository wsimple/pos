<?php
@session_start();
$_SESSION['person_id'] = $this->session->userdata('person_id');

?>
</div>
</div>
<div id="footer">
	Copyright &copy; A Repa Inc - Cellular Repair :: 2014 - <?=date('Y')?>. All Rights Reserved!&nbsp; 
	<a href="http://www.a-repa.com" target="_blank">www.a-repa.com</a>
</div>
</body>
</html>