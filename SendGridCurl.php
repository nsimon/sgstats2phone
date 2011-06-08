<?php

/******************************************************************************/
/* Module ..... SendGridCurl.php                                              */
/* Author ..... Neil Simon                                                    */
/* Modified ... 06/07/2011                                                    */
/******************************************************************************/

class SendGridCurl
    {
    public function issueRequest ($params, $display = true)
        {
        // Reset to 0 upon success
        $rc = 1;

        // Define constant for SendGrid JSON URL
        define (SENDGRID_JSON_REQUEST_URL, "http://sendgrid.com/api/mail.send.json");

        // Generate curl request
        if (($hCurl = curl_init (SENDGRID_JSON_REQUEST_URL)) == FALSE)
            {
            if ($display)
                {
                printf ("STATUS ........... ERROR: curl_init() failed\n");
                }
            }

        else
            {
            // Set the curl options
            curl_setopt ($hCurl, CURLOPT_POST,           true);     // POST
            curl_setopt ($hCurl, CURLOPT_HEADER,         false);    // Do not return headers
            curl_setopt ($hCurl, CURLOPT_RETURNTRANSFER, true);     // Return the response
            curl_setopt ($hCurl, CURLOPT_POSTFIELDS,     $params);  // Add the POST body

            // Exec curl
            if (($response = curl_exec ($hCurl)) == FALSE)
                {
                if ($display)
                    {
                    printf ("STATUS ........... ERROR: curl_exec() failed\n");
                    }
                }

            else
                {
                if ($display)
                    {
                    printf ("STATUS ........... Message sent successfully\n");
                    }

                // Success
                $rc = 0;
                }

            // Close the curl handle
            curl_close ($hCurl);
            }

        return ($rc);
        }
    }

