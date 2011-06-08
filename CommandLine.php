<?php

/******************************************************************************/
/* Module ..... CommandLine.php                                               */
/* Author ..... Neil Simon                                                    */
/* Modified ... 06/07/2011                                                    */
/******************************************************************************/

class CommandLine
    {
    protected $_argc = 0;  // Set in constructor
    protected $_argv = 0;  // Set in constructor

    public function __construct ($argc, $argv)
        {
        // Store count
        $this->_argc = $argc;

        // Store values array
        $this->_argv = $argv;
        }
    }

class CommandLine_sgstats2phone extends CommandLine
    {
    // Error constants
    const ERROR_ARG_PHONE_NOT_10      = 1;
    const ERROR_ARG_PHONE_NOT_NUMERIC = 2;

    // Extracted from command line positional values
    public $argPhone  = "";
    public $argUser   = "";
    public $argApiKey = "";

    public function __construct ($argc, $argv)
        {
        // Store argc, argv
        parent::__construct ($argc, $argv);
        }

    public function extractArgs ($display = true)
        {
        // Extract values
        $this->argPhone  = $this->_argv [1];
        $this->argUser   = $this->_argv [2];
        $this->argApiKey = $this->_argv [3];

        if ($display)
            {
            // Display the args
            printf ("argPhone ......... %s\n", $this->argPhone);
            printf ("argUser .......... %s\n", $this->argUser);
            printf ("argApiKey ........ %s\n", $this->argApiKey);
            printf ("\n");
            }
        }

    public function validateArgs ()
        {
        $rc = 1;

        // Phone must be length 10
        if (strlen ($this->argPhone) != 10)
            {
            $rc = self::ERROR_ARG_PHONE_NOT_10;
            }

        // Phone must be numeric
        elseif (!is_numeric ($this->argPhone))
            {
            $rc = self::ERROR_ARG_PHONE_NOT_NUMERIC;
            }

        else
            {
            // Success
            $rc = 0;
            }

        return ($rc);
        }

    public function printArgErrorMessage ($argErrorNum)
        {
        // Display error message
        switch ($argErrorNum)
            {
            case self::ERROR_ARG_PHONE_NOT_10:
                {
                printf ("ERROR: phone must be 10 characters.\n");
                break;
                }
            case self::ERROR_ARG_PHONE_NOT_NUMERIC:
                {
                printf ("ERROR: phone must be numeric.\n");
                break;
                }
            default:
                {
                printf ("ERROR: unknown error.\n");
                break;
                }
            }
        }
    }

