<?php
/**
 * Created by: Ajeet kanojia
 * Date: 16/11/14
 * Time: 4:52 PM
 */

require 'orm.php';
require 'sesMailer.php';


class SendCronMail
{
    public $userID = null;
    public $userName = null;
    public $userEmail = null;

    public function __construct()
    {
    }

    /*
     * Public function to count the number of user
     * and do below stuff on them
     * */
    public function countUser($user_id)
    {
        $this->userID = $user_id;

        $row = ORM::for_table('wp_user_details')->where('user_id', $this->userID)->find_one();

        $start_date = new DateTime($row['timestamp']);
        $since_start = $start_date->diff(new DateTime(date('Y-m-j H:i:s')));

        if($since_start->h >= 12 || (int)$since_start->h >= 12)
        {
            $this->sendPdf();
        }
        else
        {
            return;
        }
        
    }

    public function countLoveForecastUser($user_id)
    {
        $this->userID = $user_id;

        $row = ORM::for_table('wp_love_forecast_user_details')->where('user_id', $this->userID)->find_one();

        $start_date = new DateTime($row['timestamp']);
        $since_start = $start_date->diff(new DateTime(date('Y-m-j H:i:s')));

        if($since_start->h >= 12 || (int)$since_start->h >= 12)
        {
            $this->sendPdf();
        }
        else
        {
            return;
        }

    }

    /*
     * function for getting user name and email
     * */

    private function sendPdf()
    {
        $row = ORM::for_table('wp_user_details')->where('user_id', $this->userID)->find_one();
        $data = ORM::for_table('wp_user_details')->where('user_id', $this->userID)->find_one()->as_array();


        $data['minute'] = $data['min'];
        $data['latitude'] = $data['lat'];
        $data['longitude'] = $data['lon'];
        $data['timezone'] = $data['tzone'];

        $this->userEmail = $row['email'];
        $this->userName = $row['name'];

        if ($row['order_status'] == 'success' && $row['email_status'] == "process") {
            $mail = new SesMailer();

            $pdfLink = $this->getPdf($data,'natal_horoscope_report/tropical');

            if($pdfLink != "")
            {
                $html_data = "
                    <html>
                       <body>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Hi, " . strip_tags($this->userName) . "...</p>
    
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Thank you for your patience.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>I'm so excited for you to read your Birth Chart.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>I have completed your full Birth Chart Reading now and you can access and download it here...</p>
            
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'><a href=" . $pdfLink . ">Your Detailed Birth Chart Reading/Interpretation</a></p>
    
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>There's a LOT of personal information in this report so take your time going through it.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>Savor it like a delicious piece of fine gourmet chocolate.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>I believe you will discover a lot about yourself inside!</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>Drop me a line at review@annakovach.com and let me know your honest feedback.</p>
            
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>May the stars be on your side!</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Anna</p>
                        </body>
                    </html>
                 ";

                if($mail->sendSesMail($html_data,"Here's Your Birth Chart Reading",$this->userEmail))
                {
                    $row->set(array("email_status" => "success"));
                    $row->save();

                    return;
                }
                else
                {
                    return;
                }
            }
            else
            {
                return;
            }

        } else {
            return;
        }


    }


    private function sendLoveForecastPdf()
    {
        $row = ORM::for_table('wp_love_forecast_user_details')->where('user_id', $this->userID)->find_one();
        $data = ORM::for_table('wp_love_forecast_user_details')->where('user_id', $this->userID)->find_one()->as_array();


        $data['minute'] = $data['min'];
        $data['latitude'] = $data['lat'];
        $data['longitude'] = $data['lon'];
        $data['timezone'] = $data['tzone'];

        $this->userEmail = $row['email'];
        $this->userName = $row['name'];

        if ($row['order_status'] == 'success' && $row['email_status'] == "process") {
            $mail = new SesMailer();

            $pdfLink = $this->getPdf($data,'love_forecast_report/tropical');

            if($pdfLink != "")
            {
                $html_data = "
                    <html>
                       <body>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Hi, " . strip_tags($this->userName) . "...</p>
    
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Thank you for your patience.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>I'm so excited for you to read your Birth Chart.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>I have completed your full Birth Chart Reading now and you can access and download it here...</p>
            
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'><a href=" . $pdfLink . ">Your Detailed Birth Chart Reading/Interpretation</a></p>
    
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>There's a LOT of personal information in this report so take your time going through it.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>Savor it like a delicious piece of fine gourmet chocolate.</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>I believe you will discover a lot about yourself inside!</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>Drop me a line at review@annakovach.com and let me know your honest feedback.</p>
            
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>May the stars be on your side!</p>
                            <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Anna</p>
                        </body>
                    </html>
                 ";

                if($mail->sendSesMail($html_data,"Here's Your Birth Chart Reading",$this->userEmail))
                {
                    $row->set(array("email_status" => "success"));
                    $row->save();

                    return;
                }
                else
                {
                    return;
                }
            }
            else
            {
                return;
            }

        } else {
            return;
        }


    }


    public function sendOrderCompletionPdf($user_id)
    {
        $row = ORM::for_table('wp_user_details')->where('user_id', $user_id)->find_one();

        $_userEmail = $row['email'];
        //$this->userName = $row['name'];

        $mail = new SesMailer();

        $html_data = "
            <html>
               <body>
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Hi, it's Anna and thank you SO much for your purchase.</p>
    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Be on the lookout in your inbox for an email with the
                        subject line <b>“Here's Your Birth Chart Reading”</b>
                        so you can download your report. It should be ready within 24 hours.</p>
                        
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>There will be a LOT of information in your report, so make
                    sure to take your time to drink it all in.</p>
                    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>After you read it, I'd love to know what you think.</p>
    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>I'll be in touch with your report soon!</p>
    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Thanks!</p>
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Anna</p>
                </body>
            </html>
         ";


        if($mail->sendSesMail($html_data,"Your Birth Chart Reading Is On The Way!",$_userEmail))
        {

            //$row->set(array("email_status" => "success"));
            //$row->save();

            return;
        }
        else
        {
            return;
        }
    }


    public function sendLoveForecastOrderCompletionPdf($user_id)
    {
        $row = ORM::for_table('wp_love_forecast_user_details')->where('user_id', $user_id)->find_one();

        $_userEmail = $row['email'];
        //$this->userName = $row['name'];

        $mail = new SesMailer();

        $html_data = "
            <html>
               <body>
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Hi, it's Anna and thank you SO much for your purchase.</p>
    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Be on the lookout in your inbox for an email with the
                        subject line <b>“Here's Your Birth Chart Reading”</b>
                        so you can download your report. It should be ready within 24 hours.</p>
                        
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>There will be a LOT of information in your report, so make
                    sure to take your time to drink it all in.</p>
                    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>After you read it, I'd love to know what you think.</p>
    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;font-weight: bold'>I'll be in touch with your report soon!</p>
    
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Thanks!</p>
                    <p style='color: #1d364b;margin: 0 0 1.3em;font-size: 14px;'>Anna</p>
                </body>
            </html>
         ";


        if($mail->sendSesMail($html_data,"Your Birth Chart Reading Is On The Way!",$_userEmail))
        {

            //$row->set(array("email_status" => "success"));
            //$row->save();

            return;
        }
        else
        {
            return;
        }
    }

    private function getPDF($data,$resource)
    {

        $data1 = array(
            "cover_image" => "",
            "footer_link" => "annakovach.com",
            "logo_url" => "https://annakovach.com/wp-content/uploads/2018/04/annakovach-logo-2.png",
            "company_name" => "AnnaKovach",
            "company_info" => "",
            "domain_url" => "https://annakovach.com",
            "company_email" => "readings@annakovach.com",
            "company_landline" => "",
            "company_mobile" => ""
        );

        $reqData = array_merge($data1, $data);

        $serviceUrl = 'https://pdf.astrologyapi.com/v1'.'/'.$resource.'/';
        $authData = '601280'.":".'afb1a30b63587b048436ab04053d4dce';

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $serviceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $headers = array(
            'Authorization: Basic '. base64_encode($authData)
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($reqData));

        $response = curl_exec($ch);

        curl_close($ch);

        $resp = json_decode($response,true);

        return $resp['pdf_url'];

    }




}
