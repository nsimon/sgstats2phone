#!/usr/bin/php
<?php

/******************************************************************************/
/* Module ..... sgstats2phone.php                                             */
/* Author ..... Neil Simon                                                    */
/* Modified ... 06/07/2011                                                    */
/* Desc ....... Delivers SendGrid account stats to a phone call               */
/* Usage ...... sgstats2phone.php {phone} {username} {api_key}                */
/******************************************************************************/

/******************************************************************************/
/* Include classes                                                            */
/******************************************************************************/

require_once ("CommandLine.php");
require_once ("SendGrid.php");
require_once ("SendGridCurl.php");

/******************************************************************************/
/* main                                                                       */
/******************************************************************************/

main ($argc, $argv);

function main ($argc, $argv)
    {
    // Reset to 0 upon success
    $rc = 1;

    // 3 args (+progname) required
    if ($argc != 4)
        {
        usage ();
        printf ("ERROR: 3 args required.\n");
        }

    else
        {
        // Store args
        $commandLine = new CommandLine_sgstats2phone ($argc, $argv);

        // Extract args
        $commandLine->extractArgs ();

        // Validate args
        if (($argStatus = $commandLine->validateArgs ()) != 0)
            {
            usage ();
            $commandLine->printArgErrorMessage ($argStatus);
            }

        else
            {
            // Store the args in the sendGrid object
            $sendGrid = new SendGrid ($commandLine->argPhone,    // 3034029500
                                      $commandLine->argUser,     // nsimon@solidcode.com
                                      $commandLine->argApiKey);  // MySecretApiKey

            // Get the SendGrid stats for the current day (via REST/XML)
            if ($sendGrid->statsGet () != 0)
                {
                printf ("ERROR: sendGrid->statsGet()\n");
                }
            else
                {
                // Assemble the text-to-speech text, setup params to pass to curl
                $sendGrid->assembleStats ();

                // Send stats to phone
                if (SendGridCurl::issueRequest ($sendGrid->get_statsParams()) == 0)
                    {
                    // Successful
                    $rc = 0;
                    }
                }
            }
        }

    exit ($rc);
    }

function usage ()
    {
    printf ("Usage: sgstats2phone.php {phone} {username} {api_key}\n");
    printf ("Ex:    sgstats2phone.php 3034029500 nsimon@solidcode.com MySecretApiKey\n");
    }

