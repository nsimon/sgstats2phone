<?php

/******************************************************************************/
/* Module ..... SendGrid.php                                                  */
/* Author ..... Neil Simon                                                    */
/* Modified ... 06/07/2011                                                    */
/******************************************************************************/

class SendGrid
    {
    private $_argPhone  = "";  // Set in constructor
    private $_argUser   = "";  // Set in constructor
    private $_argApiKey = "";  // Set in constructor

    private $_statsGetXml;     // Returned from stats.get.xml
    private $_statsParams;     // Filled by assembleStats ()

    public function __construct ($argPhone, $argUser, $argApiKey)
        {
        // Store values
        $this->_argPhone  = $argPhone;
        $this->_argUser   = $argUser;
        $this->_argApiKey = $argApiKey;
        }

    public function get_statsParams ()
        {
        // This is passed to curl
        return ($this->_statsParams);
        }

    public function statsGet ()
        {
        // Reset to 0 upon success
        $rc = 1;

        // Define constant for stats get
        define (SENDGRID_XML_STATS_GET, "https://sendgrid.com/api/stats.get.xml?" .
                "api_user=$this->_argUser&api_key=$this->_argApiKey");

        // Call the SendGrid API -- suppress any errors
        if (($statsGet = @file_get_contents (SENDGRID_XML_STATS_GET)) != FALSE)
            {
            // Create addressable XML array object to pass out
            $this->_statsGetXml = new SimpleXMLElement ($statsGet);

            // Success
            $rc = 0;
            }

        return ($rc);
        }

    public function assembleStats ($display = true)
        {
        // Split-out the date
        $month = date ("F",  strtotime ($this->_statsGetXml->day->date));  // May
        $day   = date ("jS", strtotime ($this->_statsGetXml->day->date));  // 10th
        $year  = date ("Y",  strtotime ($this->_statsGetXml->day->date));  // 2011

        // Extract the stats
        $requests      = $this->_statsGetXml->day->requests;
        $delivered     = $this->_statsGetXml->day->delivered;
        $invalid_email = $this->_statsGetXml->day->invalid_email;

        if ($display)
            {
            printf ("month ............ %s\n", $month);
            printf ("day .............. %s\n", $day);
            printf ("year ............. %s\n", $year);
            printf ("\n");

            printf ("requests ......... %s\n", $requests);
            printf ("delivered ........ %s\n", $delivered);
            printf ("invalid_email .... %s\n", $invalid_email);
            printf ("\n");
            }

        // Split-out the user email into 3 pieces (split on @ and .)
        $emailPieces = preg_split ("/[@\.]/", $this->_argUser);  // nsimon solidcode com

        // Build the text-to-speech string, part 1
        $textToSpeech1 = sprintf ("send grid stats for . " .
                                  "%s . at %s dot %s . %s %s . %s . ",
                                   $emailPieces [0],
                                   $emailPieces [1],
                                   $emailPieces [2],
                                   $month,
                                   $day,
                                   $year);

        // Build the text-to-speech string, part 2
        $textToSpeech2 = sprintf ("requests . %s . delivered . %s . in valid . %s . ",
                                   $requests,
                                   $delivered,
                                   $invalid_email);

        // Build the text-to-speech string, part 3
        $textToSpeech3 = "thank you . good bye";

        if ($display)
            {
            printf ("textToSpeech1 .... %s\n", $textToSpeech1);
            printf ("textToSpeech2 .... %s\n", $textToSpeech2);
            printf ("textToSpeech3 .... %s\n", $textToSpeech3);
            printf ("\n");
            }

        // Assemble the 3 text-to-speech pieces into the fullText
        $fullText = $textToSpeech1 . $textToSpeech2 . $textToSpeech3;

        // Setup params array for curl POSTFIELDS
        $this->_statsParams = array ("api_user"  => $this->_argUser,
                                     "api_key"   => $this->_argApiKey,
                                     "x-smtpapi" => "",
                                     "to"        => $this->_argPhone . "@phone",
                                     "subject"   => "<empty>",
                                     "html"      => "",
                                     "text"      => $fullText,
                                     "from"      => "");
        }
    }

