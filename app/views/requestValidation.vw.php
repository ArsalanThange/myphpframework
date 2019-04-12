<div class="container">
<h5>Request Validation</h5>

<a href="/requestValidation?username=abc">Min Length Validation</a>
|
<a href="/requestValidation?username=abcdefghijklm">Max Length Validation</a>
|
<a href="/requestValidation?username=abcdef">No Error Validation</a>
|
<a href="/requestValidation?">Username required</a>
<?php

echo '<pre>';
echo $this->view_data;
echo '</pre>';

if (count($this->errors)) {
    echo '<ul class="collection">';
    foreach ($this->errors as $key => $value) {
?>
        <li class="collection-item">
            <?php echo $value ?>
        </li>
<?php
    }
    echo '</ul>';
}
?>
</div>