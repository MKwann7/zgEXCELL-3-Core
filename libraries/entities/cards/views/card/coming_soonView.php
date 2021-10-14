<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Card <?php echo $card->card_num; ?> Is Coming Soon | <?php echo $this->app->objCustomPlatform->getCompany()->platform_name; ?></title>
    <meta name="description" content="Demo of A Free Coming Soon Bootstrap 4 Template by TemplateFlip.com."/>
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="styles/main.css" rel="stylesheet">
</head>
<style type="text/css">
    /*
 * Globals
 */
    /* Links */
    a,
    a:focus,
    a:hover {
        color: #fff;
    }

    /* Custom default button */
    .btn-default {
        color: #fff;
        text-shadow: none;
        /* Prevent inheritence from `body` */
        background-color: transparent;
        border: 2px solid #fff;
        border-radius: 20px;
        padding: 0.5rem 2rem;
    }

    .btn-default:hover,
    .btn-default:focus {
        background-color: rgba(255, 255, 255, 0.3);
    }

    /*
     * Base structure
     */
    html,
    body {
        height: 100%;
    }

    body {
        background: url(/website/images/city-sunset.jpg) no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        text-align: center;
        font-family: 'Quicksand', sans-serif;
    }

    /* Extra markup and styles for table-esque vertical and horizontal centering */
    .site-wrapper {
        display: table;
        width: 100%;
        height: 100%;
        /* For at least Firefox */
        min-height: 100%;
        background: rgba(48, 53, 70, 0.5);
        box-shadow: inset 0 0 100px rgba(0, 0, 0, 0.5);
    }

    .site-wrapper-inner {
        display: table-cell;
        vertical-align: top;
    }

    .cover-container {
        margin-right: auto;
        margin-left: auto;
    }

    /* Padding for spacing */
    .inner {
        padding: 30px;
    }

    /*
     * Header
     */
    .masthead-brand {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .nav-masthead {
        text-align: center;
        display: block;
    }

    .nav-masthead .nav-link {
        display: inline-block;
    }

    @media (min-width: 768px) {
        .masthead-brand {
            float: left;
        }
        .nav-masthead {
            float: right;
        }
    }

    /*
     * Cover
     */
    .cover {
        padding: 0 20px;
    }

    .cover .btn-notify {
        padding: 10px 60px;
        font-weight: 500;
        text-transform: uppercase;
        border-radius: 40px;
    }

    .cover-heading {
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 10px;
        font-size: 2rem;
        margin-bottom: 5rem;
    }

    @media (min-width: 768px) {
        .cover-heading {
            font-size: 3.4rem;
            letter-spacing: 15px;
        }
    }

    .cover-copy {
        max-width: 500px;
        margin: 0 auto 3rem;
    }

    /*
     * Footer
     */
    .mastfoot {
        color: #999;
        /* IE8 proofing */
        color: rgba(255, 255, 255, 0.5);
    }

    /*
     * Subscribe modal box
     */
    #subscribeModal .modal-content {
        background-color: #303546;
        color: #fff;
        text-align: left;
    }

    #subscribeModal .modal-header, #subscribeModal .modal-footer {
        border: 0;
    }

    #subscribeModal .close {
        color: #fff;
    }

    #subscribeModal .form-control {
        margin-top: 1rem;
        background: rgba(0, 0, 0, 0.4);
        color: #fff;
    }

    #subscribeModal .form-control:focus {
        border-color: #49506a;
    }

    /*
     * Affix and center
     */
    @media (min-width: 768px) {
        /* Pull out the header and footer */
        .masthead {
            position: fixed;
            top: 0;
        }
        .mastfoot {
            position: fixed;
            bottom: 0;
        }
        /* Start the vertical centering */
        .site-wrapper-inner {
            vertical-align: middle;
        }
        /* Handle the widths */
        .masthead,
        .mastfoot,
        .cover-container {
            width: 100%;
            /* Must be percentage or pixels for horizontal alignment */
        }
    }

    @media (min-width: 992px) {
        .masthead,
        .mastfoot,
        .cover-container {
            width: 1060px;
        }
    }
</style>
<body id="top"><div class="site-wrapper">
    <div class="site-wrapper-inner">
        <div class="cover-container">
            <div class="masthead clearfix">
                <div class="inner">
                    <h3 class="masthead-brand">Card <?php echo $card->card_num; ?> Is Coming Soon</h3>
                    <nav class="nav nav-masthead" style="display:none;">
                        <a class="nav-link nav-social" href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        <a class="nav-link nav-social" href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                        <a class="nav-link nav-social" href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                        <a class="nav-link nav-social" href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                    </nav>
                </div>
            </div><br>      <div class="inner cover">
                <h1 class="cover-heading">The adventure Begins</h1>
                <p class="lead cover-copy">Hold tight as we get our working robots together to produce the most astonishing card ever.</p>
                <p class="lead" style="display:none;"><button type="button" class="btn btn-lg btn-default btn-notify" data-toggle="modal" data-target="#subscribeModal">Notify Me</button></p>
            </div>
            <div class="mastfoot">
                <div class="inner">
                    <p>&copy; <?php echo $this->app->objCustomPlatform->getCompany()->platform_name; ?></p>
                </div>
            </div>
            <div class="modal fade" id="subscribeModal" tabindex="-1" role="dialog" aria-labelledby="subscribeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="subscribeModalLabel">Get Notified on Launch:</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label for="recipient-name" class="form-control-label">Enter you e-mail to get notified when we launch</label>
                                    <input type="text" class="form-control" id="recipient-name" placeholder="your-name@example.com">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
    <script src="scripts/main.js"></script>
</body>
</html>