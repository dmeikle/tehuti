<!-- css start 
@components/twitter/includes/css/traffic.css
<!-- css end --->

<style>
    #twitter-traffic-feed .row {
        padding: 0px 20px 10px 30px;
    }
    
    #twitter-traffic-feed .row .subject{
        font-family: Verdana, Geneva, sans-serif;
        font-size: 11px;
        *font-weight: 600;
        color: #666;
    }
    
    #twitter-traffic-feed .row .date{
        text-align: left;
        font-size: 10px;
        font-family: Verdana, Geneva, sans-serif;
        margin-right: 60px;
        width: 50px;
        float: right;
    }
</style>

<div id='twitter-traffic-feed'>
    <div class="panel panel-default">
        <div class="panel-heading">
           Traffic Updates
        </div>
    <?php

        foreach($feed as $key => $value) {?>
        <div class="row">
                <div class="date"><?php echo $value['date'];?></div>
                <div class="subject"><?php echo $value['subject'];?></div>
        </div>
    <?php } ?>

    </div>
</div>

