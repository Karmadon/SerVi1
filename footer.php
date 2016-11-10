<?php
if ($loggedin)
{
	echo <<<HTML
<footer class = "text-center">Anton Stremovskyy - <a href = "http://cm.org.ua"><strong>Computer Masters</strong></a></footer>
<script src = "/Assets/js/jquery/jquery.min.js"></script>
<script src = "/Assets/js/bootstrap/bootstrap.min.js"></script>
<script src = "/Assets/js/jquery/jquery-ui.js"></script>
<script src = "/Assets/js/autocomplete.js"></script>
<script src = "/Assets/js/script.js"></script>
</body>
</html>
HTML;
}
else
{
	echo <<<HTML
<footer class = "text-center">Anton Stremovskyy - <a href = "http://computer-masters.org"><strong>Computer Masters</strong></a></footer>
</body>
</html>
HTML;
}
