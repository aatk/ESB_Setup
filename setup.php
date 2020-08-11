<?php

$SERVERHOST = "https://";
$SERVERNAME = "btrip.ru";

$agent = explode(".", $_SERVER["HTTP_HOST"])[0];


function CreatNewDir($dirinfo)
{
    unset($dirinfo[count($dirinfo) - 1]);
    
    $dirpath = [];
    foreach ($dirinfo as $dir)
    {
        $dirpath[] = $dir;
        
        $dirstring = implode("/", $dirpath);
        if (!file_exists($dirstring))
        {
            mkdir($dirstring);
        }
    }
}

if (isset($_GET["loadfilefromserver"]))
{
    $serverfilename = $_GET["filename"];
    $serverid       = $_GET["id"];
    
    $content = file_get_contents($SERVERHOST . $SERVERNAME . "/Setup/GetFile/$serverid/" . $serverfilename);
    if ($content != "")
    {
        $filedir = explode("/", $serverfilename);
        if (count($filedir) > 0)
        {
            CreatNewDir($filedir);
        }
        
        file_put_contents($serverfilename, $content);
        
        echo "loadcomplite";
    }
    else
    {
        echo "loaderror";
    }
    exit(0);
}

if (isset($_GET["htaccess"]))
{
    if (file_exists("ht.access"))
    {
        unlink(".htaccess");
        rename("ht.access", ".htaccess");
    }
    
    if (file_exists("private/settings/ht.access"))
    {
        unlink("private/settings/.htaccess");
        rename("private/settings/ht.access", "private/settings/.htaccess");
    }
    
    echo "ok";
    exit(0);
}


if (isset($_POST["setconnectors"]))
{
    //
    $Info = [
        [ "database_type" => $_POST["database_type"] ],
        [ "server" => $_POST["server"] ],
        [ "database_name" => $_POST["database_name"] ],
        [ "username" => $_POST["username"] ],
        [ "password" => $_POST["password"] ],
        [ "charset" => "utf8" ],
    ];
    
    $data = [
        "Name" => "connectionInfo",
        "Info" => $Info,
    ];
    
    $jsondata = json_encode($data, JSON_UNESCAPED_UNICODE);
    $filepath = [ "private", "settings", "connectionInfo.json" ];
    CreatNewDir($filepath);
    file_put_contents(implode("/", $filepath), $jsondata);
    
    exit(0);
}


if (isset($_POST["testdb"]))
{
    //
    $serverdata = explode(":", $_POST["server"]);
    
    $Info = [
        "database_type" => $_POST["database_type"],
        "server"        => $serverdata[0],
        "database_name" => $_POST["database_name"],
        "username"      => $_POST["username"],
        "password"      => $_POST["password"],
        "charset"       => "utf8",
    ];
    
    if (isset($serverdata[1]))
    {
        $Info["port"] = $serverdata[1];
    }
    
    $result = false;
    
    require_once 'autoload.php'; //подключаем автозагрузку доп.классов, система должна быть уже устновлена
    
    if (class_exists("db_connect"))
    {
        try
        {
            $db_connect = new db_connect($Info);
            $result     = true;
        }
        catch (Exception $e)
        {
            $result = false;
        }
    }
    
    if (!$result)
    {
        header('HTTP/1.1 500 Internal Server Error');
    }
    
    exit(0);
}


if (isset($_GET["installcore"]))
{
    
    $result = false;
    
    require_once 'autoload.php'; //подключаем автозагрузку доп.классов, система должна быть уже устновлена
    require_once 'settings.php'; //подключаем автозагрузку доп.классов
    if (class_exists("System"))
    {
        $System  = new System();
        $content = $System->UpdateSystem();
        echo $content;
        $result = true;
    }
    else
    {
        echo "CRITICAL ERROR, CORE NOT FOUND";
    }
    
    if (!$result)
    {
        header('HTTP/1.1 500 Internal Server Error');
    }
    exit(0);
}


if (isset($_POST["setsu"]))
{
    $result = false;
    require_once 'autoload.php'; //подключаем автозагрузку доп.классов, система должна быть уже установлена
    require_once 'settings.php'; //подключаем автозагрузку доп.классов
    if (class_exists("Auth"))
    {
        $Auth = new Auth();
        if ($Auth->havelogin($_POST["suusername"])["result"])
        {
            //$res = $Auth->moduser();
            $res["result"] = true;
        }
        else
        {
            $res = $Auth->adduser($_POST["suusername"], $_POST["supassword"], true, 1);
            
        }
        $result = $res["result"];
    }
    else
    {
        echo "CRITICAL ERROR, AUTH NOT FOUND";
    }
    
    if (!$result)
    {
        header('HTTP/1.1 500 Internal Server Error');
    }
    else
    {
        header('HTTP/1.1 200 OK');
        print_r("ok");
    }
    
    exit(0);
}

if (isset($_POST["finish"]))
{
    unlink("setup.php");
    if (file_exists("setup.php"))
    {
        $result = false;
    }
    else
    {
        $result = true;
    }
    
    if (!$result)
    {
        header('HTTP/1.1 500 Internal Server Error');
    }
    
    exit(0);
}


?>


<!doctype html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Setup BTRIP API / CMS</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="">
    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-json/2.6.0/jquery.json.min.js" integrity="sha256-Ac6pM19lP690qI07nePO/yY6Ut3c7KM9AgNdnc5LtrI=" crossorigin="anonymous"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <link href="<?php echo $SERVERHOST . $SERVERNAME ?>/js/lib/SmartWizard/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo $SERVERHOST . $SERVERNAME ?>/js/lib/SmartWizard/js/jquery.smartWizard.min.js" type="text/javascript"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

    <script type="text/javascript"
            src="<?php echo $SERVERHOST . $SERVERNAME ?>/js/controllers/btripcontrollers.js"></script>


    <style>
        @font-face {
            font-family: 'Pe-icon-7-stroke';
            /*            src: url(*/
        <?php// echo $SERVERHOST . $SERVERNAME ?>/*/assets/fonts/Pe-icon-7-stroke.eot);*/
            src: url(<?php echo $SERVERHOST . $SERVERNAME ?>/assets/fonts/Pe-icon-7-stroke.eot?#iefixd7yf1v) format("embedded-opentype"), url(<?php echo $SERVERHOST . $SERVERNAME ?>/assets/fonts/Pe-icon-7-stroke.woff) format("woff"), url(<?php echo $SERVERHOST . $SERVERNAME ?>/assets/fonts/Pe-icon-7-stroke.ttf) format("truetype"), url(<?php echo $SERVERHOST . $SERVERNAME ?>/assets/fonts/Pe-icon-7-stroke.svg#Pe-icon-7-stroke) format("svg");
            font-weight: normal;
            font-style: normal
        }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: .88rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            text-align: left;
            background-color: #fff;
        }

        .app-main .app-main__inner {
            padding: 30px 30px 0;
            flex: 1;
        }

        .app-page-title {
            padding: 30px;
            /*margin: -30px -30px 30px;*/
            position: relative;
        }

        .app-page-title .page-title-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .app-page-title .page-title-heading {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-content: center;
            align-items: center;
        }

        .app-page-title .page-title-icon {
            font-size: 2rem;
            display: flex;
            align-items: center;
            align-content: center;
            text-align: center;
            padding: .83333rem;
            margin: 0 30px 0 0;
            background: #fff;
            box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03), 0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05), 0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
            border-radius: 2rem;
            width: 60px;
            height: 60px;
        }

        .app-page-title .page-title-subheading {
            padding: 3px 0 0;
            font-size: .88rem;
            opacity: .6;
            font-weight: normal;
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1.25rem;
        }

        .card.mb-3 {
            margin-bottom: 30px !important;
        }

        .card {
            box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03), 0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03), 0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05), 0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
            border-width: 0;
            transition: all .2s;
        }

        .fixed-sidebar .app-main .app-main__outer {
            z-index: 9;
            /*padding-left: 280px;*/
        }

        .app-main .app-main__outer {
            flex: 1;
            flex-direction: column;
            display: flex;
            z-index: 12;
        }

        .app-theme-white.app-container {
            background: #f1f4f6;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            margin: 0;
        }

        ,

        [class^="pe-7s-"], [class*=" pe-7s-"] {
            display: inline-block;
            font-family: 'Pe-icon-7-stroke';
            speak: none;
            font-style: normal;
            font-weight: normal;
            font-variant: normal;
            text-transform: none;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale
        }

    </style>
</head>
<body>

<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar">
    <div class="app-main">

        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-graph text-success">
                        </i>
                    </div>
                    <div>Setup I4B API / CMS
                        <div class="page-title-subheading">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-main__inner">

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">


                            <div id="smartwizard">
                                <ul>
                                    <li><a href="#step-1">License<br />
                                            <small>License information</small>
                                        </a></li>
                                    <li><a href="#step-2">Download<br />
                                            <small>Download system</small>
                                        </a></li>
                                    <li>
                                        <a href="#step-3">MySQL<br />
                                            <small>Setup DB</small>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#step-4">Install CORE<br />
                                            <small>Setup core</small>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#step-5">Set SU<br />
                                            <small>Setup SU</small>
                                        </a>
                                    </li>
                                    <!--                                    <li>-->
                                    <!--                                        <a href="#step-6">Addon<br/>-->
                                    <!--                                            <small>Install Addon</small>-->
                                    <!--                                        </a>-->
                                    <!--                                    </li>-->
                                    <li>
                                        <a href="#step-7">Finish<br />
                                            <small>End Install</small>
                                        </a>
                                    </li>
                                </ul>

                                <div>
                                    <div id="step-1" class="">
                                        <div class="card-body">
                                            <h2>License</h2>
                                            <p>Released under the terms of the MIT License (<a
                                                        href="http://en.wikipedia.org/wiki/MIT_License">more</a>).
                                                You are free to use on both personal and commercial environment as long
                                                as
                                                the
                                                copyright header
                                                is
                                                left
                                                intact.</p>
                                            <br />
                                            <p>MIT License</p>


                                            <p>Copyright (c) 2020 Tkachenko Alexander<br />
                                                <a href="https://btrip.ru">https://btrip.ru</a> <a href="https://info4b.ru">https://info4b.ru</a>
                                            </p>
                                            <p>
                                                Permission is hereby granted, free of charge, to any person obtaining a
                                                copy<br />
                                                of this software and associated documentation files (the "Software"), to
                                                deal<br />
                                                in the Software without restriction, including without limitation the
                                                rights<br />
                                                to use, copy, modify, merge, publish, distribute, sublicense, and/or
                                                sell<br />
                                                copies of the Software, and to permit persons to whom the Software
                                                is<br />
                                                furnished to do so, subject to the following conditions:
                                            </p>
                                            <p>
                                                The above copyright notice and this permission notice shall be included
                                                in
                                                all<br />
                                                copies or substantial portions of the Software.
                                            </p>
                                            <p>
                                                THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
                                                OR<br />
                                                IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
                                                MERCHANTABILITY,<br />
                                                FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
                                                THE<br />
                                                AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
                                                OTHER<br />
                                                LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
                                                FROM,<br />
                                                OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
                                                IN
                                                THE<br />
                                                SOFTWARE.
                                            </p>
                                        </div>
                                    </div>
                                    <div id="step-2" class="">
                                        <div class="card-body">
                                            <a id="loadfiles" class="mb-2 mr-2 btn-hover-shine btn btn-primary">Load files for install</a>
                                            <table id="filestable" class="table table-striped table-bordered"
                                                   style="width:100%">
                                                <thead>
                                                <tr>
                                                    <td>Check</td>
                                                    <td>File</td>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="step-3" class="">
                                        <div class="card-body">
                                            <form id="FormData">
                                                <input type="hidden" name="setconnectors" value="true">
                                                <div class="position-relative form-group">
                                                    <label for="database_type">Database Type</label>
                                                    <input name="database_type"
                                                           id="database_type"
                                                           placeholder="mysql"
                                                           type="text"
                                                           value="mysql"
                                                           class="form-control" data-validation="required">
                                                </div>
                                                <div class="position-relative form-group">
                                                    <label for="database_type">Server</label>
                                                    <input name="server"
                                                           id="server"
                                                           placeholder="localhost"
                                                           type="text"
                                                           value="localhost"
                                                           class="form-control" data-validation="required">
                                                </div>
                                                <div class="position-relative form-group">
                                                    <label for="database_name">Database Name</label>
                                                    <input name="database_name"
                                                           id="database_name"
                                                           placeholder="localhost"
                                                           type="text"
                                                           value="<?php echo $agent; ?>"
                                                           class="form-control" data-validation="required">
                                                </div>

                                                <div class="position-relative form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="position-relative form-group">
                                                                <label for="username">Database Username</label>
                                                                <input
                                                                        name="username" id="username"
                                                                        placeholder="Username"
                                                                        type="text"
                                                                        value="root"
                                                                        class="form-control" data-validation="required">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="position-relative form-group">
                                                                <label for="password">Database Password</label>
                                                                <input
                                                                        name="password" id="password"
                                                                        placeholder="Password"
                                                                        type="password" class="form-control" data-validation="required">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <a id="testdb" class="mb-2 mr-2 btn-hover-shine btn btn-primary">TEST
                                                    CONNECT TO
                                                    DATABASE</a>

                                                <div class="position-relative form-group">
                                                    <a id="setdata" class="mb-2 mr-2 btn-hover-shine btn btn-primary">Send DB information</a>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                    <div id="step-4" class="">
                                        <div class="card-body">
                                            <a id="installcore" class="mb-2 mr-2 btn-hover-shine btn btn-primary">Install
                                                CORE</a>
                                            <div id="logconsole" style="width: 100%; height: 100%">

                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-5" class="">
                                        <div class="card-body">
                                            <form id="FormDataSU">
                                                <input type="hidden" name="setsu" value="true">

                                                <div class="position-relative form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="position-relative form-group">
                                                                <label for="suusername">Super user name (Admin)</label>
                                                                <input
                                                                        name="suusername" id="suusername"
                                                                        placeholder="Username"
                                                                        type="text"
                                                                        value="root"
                                                                        data-validation="required"
                                                                        class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="position-relative form-group">
                                                                <label for="supassword">Super user password</label>
                                                                <input
                                                                        name="supassword" id="supassword"
                                                                        placeholder="Password"
                                                                        data-validation="required"
                                                                        type="password" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="position-relative form-group">
                                                    <a id="setsu" class="mb-2 mr-2 btn-hover-shine btn btn-primary">Set
                                                        SU</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <!--                                    <div id="step-6" class="">-->
                                    <!--                                        <div class="card-body">-->
                                    <!--                                            <h2>Select Addons</h2>-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <div id="step-7" class="">
                                        <div class="card-body">
                                            <h2>CONGRATULATIONS!!!</h2>
                                            <a id="unlinksetup" class="mb-2 mr-2 btn-hover-shine btn btn-primary">Click this button for end Install</a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript"
        src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>


<script>
    var x                = window.location;
    var servername       = x.origin;
    var remoteservername = "<?php echo $SERVERHOST . $SERVERNAME ?>";


    var Model = {
    
        CheckList: {
            'LoadCore'   : false,
            'DBConnected': false,
            'InstallCore': false,
            'SetSU'      : false
        },
    
        OnLoad: function ()
        {
        
            $.ajaxSetup({
                async: false
            });
        
            $('#smartwizard').smartWizard({
                selected       : 0,
                theme          : 'arrows',
                toolbarSettings: {
                    toolbarPosition      : 'top', // none, top, bottom, both
                    toolbarButtonPosition: 'right', // left, right
                    showNextButton       : true, // show/hide a Next button
                    showPreviousButton   : true // show/hide a Previous button
                }
            });
        
            $("#smartwizard").on("leaveStep", Model.ListSteps);
        
            $('#loadfiles').on("click", Model.GetFileList);
        
            $('#testdb').on("click", Model.TestDB);
            $('#setdata').on("click", Model.SetDB);
            $('#installcore').on("click", Model.InstallCore);
        
            $('#setsu').on("click", Model.SetSU);
        
            $('#unlinksetup').on("click", Model.Finish);
        
            $.validate();
            //$('#filestable').DataTable();
        },
    
        ListSteps: function (e, anchorObject, stepNumber, stepDirection)
        {
        
            var result = false;
        
            if (stepNumber == 0)
            {
                result = true;
            }
        
            else if ((stepNumber == 1) && (stepDirection == "forward"))
            {
                if (Model.CheckList['LoadCore'] == false)
                {
                    Model.GetFileList();
                    result = true;
                }
                else
                {
                    result = true;
                }
            }
        
            else if ((stepNumber == 2) && (stepDirection == "forward"))
            {
                if (Model.CheckList['DBConnected'] == false)
                {
                    if ($("#FormData").isValid())
                    {
                        if (Model.TestDB())
                        {
                            Model.SetDB();
                            result = true;
                        }
                    
                    }
                }
                else
                {
                    result = true;
                }
            }
        
            else if ((stepNumber == 3) && (stepDirection == "forward"))
            {
                //
                if (Model.CheckList['InstallCore'] == false)
                {
                    result = Model.InstallCore();
                    result = true;
                }
                else
                {
                    result = true;
                }
            }
        
            else if ((stepNumber == 4) && (stepDirection == "forward"))
            {
                //
                if (Model.CheckList['SetSU'] == false)
                {
                    if ($("#FormDataSU").isValid())
                    {
                        result = Model.SetSU();
                    }
                }
                else
                {
                    result = true;
                }
            }
        
            else
            {
                result = true;
            }
        
            return result;
        },
    
        ShowStep: function (Step)
        {
            document.location = servername + "/setup.php#step-" + Step;
        },
    
        /* STEP 2 */
        LoadFileFromServer: function (id, filename)
        {
        
            var FormData = {
                "loadfilefromserver": true,
                "filename"          : filename,
                "id"                : id
            };
            var p        = $.get(servername + "/setup.php", FormData);
            p.done(function (data)
            {
                Controllers.PushNotificatoin("Load file " + filename);
            });
            p.fail(function (data)
            {
                Controllers.PushNotificatoin("Error load file " + filename);
            });
        
        },
    
        GetFileList: function ()
        {
            var p = $.get(remoteservername + "/Setup/GetFileList");
            p.done(function (data)
            {
            
                var id    = data.id;
                var files = data.files;
                Controllers.PushNotificatoin("Load file list");
            
                var filestable = $('#filestable').DataTable({
                    "paging"  : false,
                    "ordering": false,
                    "info"    : false
                });
                $.each(files, function ($key, $value)
                {
                    checkfile = Model.LoadFileFromServer(id, $value);
                    filestable.row.add([
                        "",
                        $value
                    ]).draw(false);
                });
            
                Model.UnlinkHT();
                Model.CheckList['LoadCore'] = true;
            });
            p.fail(function (data)
            {
                Controllers.PushNotificatoin("Error load file list");
            });
        },
    
        UnlinkHT: function ()
        {
            var FormData = {
                "htaccess": true,
            };
            var p        = $.get(servername + "/setup.php", FormData);
            p.done(function (data)
            {
                Controllers.PushNotificatoin("Complite install");
            });
            p.fail(function (data)
            {
                Controllers.PushNotificatoin("Error install");
            });
        },
    
    
        /* STEP 3 */
        TestDB: function ()
        {
            //
            var result   = false;
            var FormData = $("#FormData").serializeArray();
        
            delete FormData[0];
            FormData[0] = {name: "testdb", value: true};
        
            var p = $.post(servername + "/setup.php", FormData);
            p.done(function (data)
            {
                Controllers.PushNotificatoin("Test DB Connection");
                result = true;
            });
            p.fail(function (data)
            {
                Controllers.PushNotificatoin("Error Test DB Connection");
            });
        
            return result;
        },
    
        SetDB: function ()
        {
            if (!$("#FormData").isValid())
            {
                Controllers.PushNotificatoin("Error valid form");
            }
            else
            {
            
                var FormData = $("#FormData").serializeArray();
                var p        = $.post(servername + "/setup.php", FormData);
            
                p.done(function (data)
                {
                    Controllers.PushNotificatoin("Install data");
                    Model.CheckList['DBConnected'] = true;
                });
                p.fail(function (data)
                {
                    Controllers.PushNotificatoin("Error install data ");
                });
            }
        },
    
        /* STEP 4 */
        InstallCore: function ()
        {
            var result   = false;
            var FormData = {
                "installcore": true
            };
            var p        = $.get(servername + "/setup.php", FormData);
            p.done(function (data)
            {
                $("#logconsole").html(data);
                Controllers.PushNotificatoin("Install CORE");
                Model.CheckList['InstallCore'] = true;
                result                         = true;
            });
            p.fail(function (data)
            {
                Controllers.PushNotificatoin("Error install CORE ");
            });
        
            return result;
        },
    
        /* STEP 5 */
        SetSU: function ()
        {
            var result = false;
        
            if (!$("#FormDataSU").isValid())
            {
                Controllers.PushNotificatoin("Error valid form");
            }
            else
            {
                var FormData = $("#FormDataSU").serializeArray();
                var p        = $.post(servername + "/setup.php", FormData);
                p.done(function (data)
                {
                    Controllers.PushNotificatoin("Install SU");
                    Model.CheckList['SetSU'] = true;
                    result                   = true;
                });
                p.fail(function (data)
                {
                    Controllers.PushNotificatoin("Error install SU");
                });
            }
        
            return result;
        },
    
        Finish: function ()
        {
            var FormData = {
                "finish": true
            };
            var p        = $.post(servername + "/setup.php", FormData);
            p.done(function (data)
            {
                document.location = servername;
            });
            p.fail(function (data)
            {
                Controllers.PushNotificatoin("Error finish install");
            });
        
        }
    
    };


    $(document).ready(Model.OnLoad);

</script>

</body>
</html>

