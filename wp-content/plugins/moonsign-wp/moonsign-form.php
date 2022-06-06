<?php

function buildDays()
{
    $html = '';
    for ($i = 1; $i <= 31; $i++) {
        $html .= '<option value="' . $i . '">' . $i . '</option>';
    }
    return $html;
}

function buildMonths()
{
    $html = '';
    for ($i = 1; $i <= 12; $i++) {
        $dateObj = DateTime::createFromFormat('!m', $i);
        $monthName = $dateObj->format('F');
        $html .= '<option value="' . $i . '">' . $monthName . '</option>';
    }
    return $html;
}

function buildYears()
{
    $html = '';
    for ($i = 2005; $i >= 1920; $i--) {
        $html .= '<option value="' . $i . '">' . $i . '</option>';
    }
    return $html;
}

function buildHours()
{
    $html = '';
    for ($i = 0; $i <= 24; $i++) {
        $value = ($i > 12) ? ($i - 12) . ' PM' : $i . ' AM';
        $html .= '<option value="' . $i . '">' . $value . '</option>';
    }
    return $html;
}

function buildMinutes()
{
    $html = '';
    for ($i = 1; $i <= 59; $i++) {
        $html .= '<option value="' . $i . '">' . $i . '</option>';
    }
    return $html;
}

function buildForm()
{
    $fontSize = get_option('moonsign_calculator_heading_size') ? get_option('moonsign_calculator_heading_size') : '';
    $position = get_option('moonsign_calculator_heading_position') ? get_option('moonsign_calculator_heading_position') : '';
    $color = get_option('moonsign_calculator_heading_color') ? get_option('moonsign_calculator_heading_color') : '';
    $marginBottom = get_option('moonsign_calculator_heading_margin_bottom') ? get_option('moonsign_calculator_heading_margin_bottom') : '';
    $marginTop = get_option('moonsign_calculator_heading_margin_top') ? get_option('moonsign_calculator_heading_margin_top') : '';
    $buttonColor = get_option('moonsign_calculator_heading_button_color') ? get_option('moonsign_calculator_heading_button_color') : '';
    $buttonPadding = get_option('moonsign_calculator_heading_button_padding') ? get_option('moonsign_calculator_heading_button_padding') : '';

    $html = '<div class="py-2 mb-3 border rounded moonsign-form-wrap">' .
        '<h2 style="
margin-bottom: ' . $marginBottom . 'px;
margin-top: ' . $marginTop . 'px; 
font-size: ' . $fontSize . 'px; 
color: ' . $color . '; 
text-align: ' . $position . '">Moon Sign Calculator</h2>' .
        '<form method="post" class="moonsign-form">

            <div class="">
                <div class="col-auto">
                    <label class="col-form-label">Day of birth</label>
                    <select name="moonsign-d" class="form-control" required="">
                        <option value="" selected="" disabled="" hidden=""></option>'
        . buildDays() .
        '</select>
                </div>
                
                <div class="col-auto">
                    <label class="col-form-label">Month of birth</label>
                    <select name="moonsign-m" class="form-control" required="">
                        <option value="" selected="" disabled="" hidden=""></option>'
        . buildMonths() .
        '</select>
                </div>
                
                <div class="col-auto">
                    <label class="col-form-label">Year of birth</label>
                    <select name="moonsign-y" class="form-control" required="">
                        <option value="" selected="" disabled="" hidden=""></option>'
        . buildYears() .
        '</select>
                </div>
                
                <div class="col-auto col-auto-mobile" style="overflow: hidden">
                    <label class="col-form-label">Time Of Birth</label>
                    <div class="col-inner">
                        <select name="moonsign-h" class="form-control" required="">
                            <option value="NA">Hours</option>'
        . buildHours() .
        '</select>
                    </div>
                    <div class="col-inner">
                        <select name="moonsign-i" class="form-control" required="">
                            <option value="NA">Minutes</option>'
        . buildMinutes() .
        '</select>
                    </div>
                </div>
                
                <div class="col-auto">
                <label class="col-form-label">Time Zone</label>
                    <select name="moonsign-zone" class="form-control">
                        <option value="NA" selected="selected">TimeZone</option>
                        <option value="-11">-11 Magadan, Solomon Islands</option>
                        <option value="-10">-10 Australian Eastern</option>
                        <option value="-9">-9 Japan, Australian Central</option>
                        <option value="-8">-8 Australian Western</option>
                        <option value="-7">-7 South Sumatran</option>
                        <option value="-6">-6 Russia Zone 5</option>
                        <option value="-5">-5 Russia Zone 4</option>
                        <option value="-4">-4 Russia Zone 3</option>
                        <option value="-3">-3 Baghdad </option>
                        <option value="-2">-2 East Europe, Brit War Time</option>
                        <option value="-1">-1 British Summer Time</option>
                        <option value="0">0 Greenwich Mean</option>
                        <option value="1">1 Azores</option>
                        <option value="2">2 Mid Atlantic</option>
                        <option value="3">3 Atlantic Daylight</option>
                        <option value="4">4 Eastern Daylight</option>
                        <option value="5">5 Central Daylight</option>
                        <option value="6">6 Central </option>
                        <option value="7">7 Mountain</option>
                        <option value="8">8 Pacific</option>
                        <option value="9">9 Yukon</option>
                        <option value="10">10 Alaska-Hawaii</option>
                        <option value="11">11 Midway Island</option>
                    </select>
                </div>
            </div>
            
            <div class="col-auto-full">
                <button
                    type="button" 
                    class="moonsign-submit btn btn-primary">Calculate!</button>
            </div>
        </form>
    </div>';

    /* style="color:' . $buttonColor . '; padding: ' . $buttonPadding . '" */

    return $html;
}