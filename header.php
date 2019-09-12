            
            <?php
            $ctime = time();
            $ct = $ctime + 3;
            $conline = mysqli_query($conn, "SELECT * from user where online = 1 or online_status < '$ct'");
            $numm = mysqli_num_rows($conline);
            ?>
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <!-- nav and search button -->
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div><!-- 
                        <div class="search-box pull-left">
                            <form action="#">
                                <input type="text" name="search" placeholder="Search..." required>
                                <i class="ti-search"></i>
                            </form>
                        </div> -->
                    </div>
                    <!-- profile info & task notification -->
                    
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area pull-right">
                            <li id="full-view"><i class="ti-fullscreen"></i></li>
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                            <li class="dropdown">
                                <i class="ti-bell dropdown-toggle" data-toggle="dropdown">
                                    <span><?php echo $numm; ?></span>
                                </i>
                                <div class="dropdown-menu bell-notify-box notify-box">
                                    <span class="notify-title">Currently Online</span>
                                    <div class="nofity-list">
                                        <a href="#" class="notify-item">
                                            <div class="notify-text">
                                                <?php
                                                    while ($cow = mysqli_fetch_array($conline)) {
                                                        $ccname = $cow['user_name'];
                                                    echo '
                                                <p> - '.$ccname.'</p>
                                                 '; } ?>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- header area end -->