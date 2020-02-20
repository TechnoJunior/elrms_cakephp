<!--header start-->
<header class="header dark-bg">
    <div class="toggle-nav">
        <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="bottom"><i class="icon_menu"></i></div>
    </div>
    <!--logo start-->
    <a href="index.html" class="logo">e<span class="lite">LRMS</span></a>
    <!--logo end-->

    <div class="top-nav notification-row">
        <!-- notification dropdown start-->
        <ul class="nav pull-right top-menu">
            <!-- user login dropdown start-->
            <li class="dropdown">
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="profile-ava">
                        <?= $this->Html->image('favicon.png',['alt'=>'NIC Logo','height'=>'30','width'=>'30'])?>
                        
                    </span>
                    <span class="username">Collector</span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu extended logout">
                    <div class="log-arrow-up"></div>
                    <li class="eborder-top">
                        <a href="#"><i class="icon_profile"></i> My Profile</a>
                    </li>
                    <li>
                        <a href="login.html"><i class="icon_key_alt"></i> Log Out</a>
                    </li>
                    <li>
                        <a href="documentation.html"><i class="icon_key_alt"></i> Documentation</a>
                    </li>
                    <li>
                        <a href="documentation.html"><i class="icon_key_alt"></i> Documentation</a>
                    </li>
                </ul>
            </li>
            <!-- user login dropdown end -->
        </ul>
        <!-- notificatoin dropdown end-->
    </div>
</header>
<!--header end-->