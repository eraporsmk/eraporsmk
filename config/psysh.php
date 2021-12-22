<?php
// Temporary fix for issue #34 in laravel tinker where psysh
// tries to run in `/run/user/<uid>/psysh`.
//
// This overrides that behaviour to `.psysh/` inside the project
return [
	'runtimeDir'	=> './.psysh',
];