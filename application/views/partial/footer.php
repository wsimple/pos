<?php
@session_start();
$_SESSION['person_id'] = $this->session->userdata('person_id');

?>
</div>
</div>
<div id="footer">
	Copyright &copy; DASH - Cellular Repair :: 2010 - <?=date('Y')?>. All Rights Reserved!&nbsp; 
	<a href="http://www.om-parts.com/home/" target="_blank">www.om-parts.com/home</a>
</div>
</body>
</html>