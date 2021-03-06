<?php
include_once 'api.php';

$urls = $api->getUrls();
$options = $api->getOptions();

//    $seconds = (isset($options['speed'])) ? $options['speed'] : DEFAULT_ROTATE_SPEED;   
$seconds = (isset($urls[0]['rotate'])) ? $urls[0]['rotate'] : 30; 

$id = isset($_GET['id']) ? (int) $_GET['id'] : 1;

if ($id > count($urls)) {
   $id = 1;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Full Page Dashboard</title>

    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

      <!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->

  </head>
  <script src="js/iframeResizer.js"></script>
  <body style="background-color: black;">
    <div id="container" style="overflow: hidden">
        <div class="show_nav">
            <img src="img/settings-icon.png" />
        </div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" style="display: none" id="dashboard_tab">
            <?php if (count($urls) > 1) : ?>
                <li>
                    <a href="javascript:void(0)" onclick="changeCounter()">
                        <input type="text" id="counter" class="dial">
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <span id="pause-btn" onclick="stop()" class="glyphicon glyphicon-pause"></span>
                        <span id="play-btn" onclick="play()" class="glyphicon glyphicon-play hidden"></span>
                    </a>
                </li>
            <?php endif; ?>
            <?php foreach ($urls as $key => $url) : ?>
                <li role="presentation" <?php echo ($key==0 ) ? 'class="active"' : ''; ?> data-whichkey="<?php echo $key;?>" data-rotate="<?php echo $url['rotate'];?>" data-name="<?php echo $url['title'];?>">
                    <a href="#url<?php echo $key;?>" aria-controls="url<?php echo $key;?>" role="tab" data-toggle="tab" data-easein="fadeIn">
                        <?php echo $url['title']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li role="action"><a href="#" onclick="add()" aria-controls="add" >+</a></li>
            <?php if (count($urls) > 1) : ?>
                <li role="action" class="pull-right" style="margin-top: 2px;padding-right: 45px;">
                    <a href="#" onclick="deleteUrl()">
                        <span id="play-btn" class="glyphicon glyphicon-trash"></span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="overflow: hidden" id="dashboard_content">
            <?php foreach ($urls as $key => $url) : ?>
                <div role="tabpanel" style="overflow: hidden" class="tab-pane <?php echo ($key == 0) ? 'active' : ''; ?>" id="url<?php echo $key; ?>">
                    <iframe src="<?php echo $url['url']; ?>" id="frame-<?php echo $key; ?>" style="width:1920px; height:1080px; border:none; margin:0; padding:0; overflow:hidden;z-index: 0;" onload="iFrameResize({}, this)" allowtransparency>
                      Your browser doesn't support iframes
                  </iframe>
              </div>
          <?php endforeach; ?>
      </div>

  </div>

  <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add URL</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="add-title" class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="add-title" placeholder="Title">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-url" class="col-sm-2 control-label">URL</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="add-url" placeholder="URL">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-url" class="col-sm-2 control-label">Rotate</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="add-rotate" placeholder="Rotate" value="30">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="addUrl()">Add</button>
            </div>
        </div>
    </div>
</div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.knob.min.js"></script>


<script type="text/javascript">
    var rotateTime = <?php echo $seconds; ?>;
    var counter = rotateTime;
    var timer = null;

    function resize() {
        if ($('.setting-active').length)
        {
            $('#container').css('height', $(window).height());
            $('.tab-content').css('height', $(window).height() - 80 );
            //$('iframe').css('height', $(window).height() - 80 );
        }
        else
        {
            $('#container').css('height', $(window).height());
            $('.tab-content').css('height', $(window).height() - 10 ); // - $('.nav-tabs').height() - 5);
            //$('iframe').css('height', $(window).height() - 10 );
        }
    }

    function nextTab() {
        var next = $('.nav-tabs li.active').next('li');
        var new_rotate = next.data('rotate');


        if (next.text() == '+') {
            $($('.nav-tabs li')[2]).find('a').click();
            var new_rotate = $($('.nav-tabs li')[2]).data('rotate');
        } else if (!new_rotate) {
            $($('.nav-tabs li')[2]).find('a').click();
            var new_rotate = $($('.nav-tabs li')[2]).data('rotate');
        } else {
            $('.nav-tabs li.active').next('li').find('a').click();
        }
        document.title = 'Full Page Dashboard - ' + $('.nav-tabs li.active a').text();
        if(new_rotate){
            counter = new_rotate;
        }
    }

    function play() {
        timer = setInterval(function() {
            tick();
        }, 1000);
        $('#pause-btn').removeClass('hidden');
        $('#play-btn').addClass('hidden');
    }

    function stop() {
        clearInterval(timer);
        $('#pause-btn').addClass('hidden');
        $('#play-btn').removeClass('hidden');
    }

    function initKnob(maxValue) {
        $("#counter").knob({
            width: 20,
            height: 20,
            displayInput: false,
            min: 0,
            max: maxValue,
            readOnly: true
        });
    }

    function add() {
        stop();
        $('#addModal').modal();
    }

    $(document).ready(function() {

        <?php if (count($urls) > 1) : ?>
        initKnob(rotateTime);
        play();
        document.title = 'Full Page Dashboard - ' + $('.nav-tabs li.active a').text();
    <?php endif; ?>
    resize();
});

    function tick() {
        $('#counter').val(counter).trigger('change');
        counter--;
        if (counter < 0) {
            counter = rotateTime;
            nextTab();
        }
    }

    $(window).resize(function() {
        resize();
    });

    function deleteUrl() {
        var title = $('.nav-tabs li.active a').text();
        var id = $('.nav-tabs li.active a').attr('href').replace('#url', '');

        var r = confirm('Are you sure you would to delete "' + title + '"');
        if (r == true) {
            $.ajax({
                type: "POST",
                url: 'api.php',
                data: {
                    'action': 'delete',
                    'title': title,
                    'id': id
                },
                success: function(data, textStatus, jqXHR) {
                    window.location.reload()
                }
            });
        }
    }

    function isNormalInteger(str) {
        var n = ~~Number(str);
        return String(n) === str && n >= 0;
    }

    function changeCounter() {
        var which_key = $('.nav-tabs .active').data('whichkey');
        var key_rotate = $('.nav-tabs .active').data('rotate');
        var key_title = $('.nav-tabs .active').data('name');
        var newCounter = prompt("Select rotate time for " + key_title, key_rotate);

        if (newCounter != null && isNormalInteger(newCounter)) {
            newCounter = parseInt(newCounter);
            $.ajax({
                type: "POST",
                url: 'api.php',
                data: {
                    'action': 'speed',
                    'value': newCounter,
                    'id' : which_key
                },
                success: function(data, textStatus, jqXHR) {
                    window.location.reload();
                }
            });
        }


    }

    function addUrl() {
        var title = $('#add-title').val();
        var url = $('#add-url').val();
        var rotate = $('#add-rotate').val();

        if (title == '' || url == '' || rotate == '') {
            alert('Title and URL cannot be empty');
        }

        $.ajax({
            type: "POST",
            url: 'api.php',
            data: {
                'action': 'add',
                'title': title,
                'url': url,
                'rotate':rotate,
            },
            success: function(data, textStatus, jqXHR) {
                window.location.reload();
            }
        });
    }

</script>

<script>

    $('.show_nav').click(function() {
        if($(this).hasClass("setting-active")) {
            $('.nav-tabs').hide();   
            $(this).removeClass("setting-active");
            play();
            resize();
        } else {
            $('.nav-tabs').show();   
            $(this).addClass("setting-active");
            stop();
            resize();
        }
    }); 

    var animations= ['zoomIn','fadeIn','fadeInLeft', 'fadeInRight', 'slideInLeft', 'slideInRight'];
    function randomAnimation()
    {
        var animation = animations[Math.floor(Math.random() * animations.length)];
        return animation;
    }

    $(function(){var b="fadeInLeft";var c;var a;d($("#dashboard_tab a"),$("#dashboard_content"));function d(e,f,g){e.click(function(i){i.preventDefault();$(this).tab("show");var h=$(this).data("easein");if(c){c.removeClass(a);}if(h){f.find("div.active").addClass("animated "+h);a=h;}else{if(g){f.find("div.active").addClass("animated "+g);a=g;}else{f.find("div.active").addClass("animated "+randomAnimation());a=b;}}c=f.find("div.active");});}$("a[rel=popover]").popover().click(function(f){f.preventDefault();if($(this).data("easein")!=undefined){$(this).next().removeClass($(this).data("easein")).addClass("animated "+$(this).data("easein"));}else{$(this).next().addClass("animated "+randomAnimation());}});});

</script>
</html>
