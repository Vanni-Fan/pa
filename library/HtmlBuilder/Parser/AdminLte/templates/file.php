<?php

$this->js();
$this->script("$('#fileupload').fileupload();");
?>

<input id="fileupload" type="file" name="files[]" multiple
       data-url="/path/to/upload/handler.json"
       data-sequential-uploads="true"
       data-form-data='{"script": "true"}'>