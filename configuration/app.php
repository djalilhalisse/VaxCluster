<?php
// Path to the Python interpreter used for predictions.
// Set the PYTHON_PATH environment variable, or edit the fallback below.
// Windows example: 'C:/ProgramData/anaconda3/python.exe'   Linux/macOS: 'python3'
define('PYTHON_PATH', getenv('PYTHON_PATH') ?: 'C:/ProgramData/anaconda3/python.exe');
