<?php


$query = "
ALTER TABLE user ADD lasttimeonline VARCHAR(50);
";

query($query);