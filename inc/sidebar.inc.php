<nav class="sidebar close">
    <header>
        <div class="image-text">
            <span class="image">
                <img src="assets/img/GILILOGO3.png" alt="">
            </span>

            <div class="text logo-text">
                <span class="name">GLACIER </span>
                <span class="profession"></span>
            </div>
        </div>
        <i class='bx bx-chevron-right toggle'></i>
    </header>
    <!-- HOVER SUBLINK START-->
    <div>


        <ul class="sublink-hover" id="sub-nav-hover0">
            <li class="sublink-item text-secondary" id="sublinkHeader" aria-disabled="true">DASHBOARD</li>
        </ul>
        <ul class="sublink-hover" id="sub-nav-hover1">
            
            <li class="sublink-item text-secondary" id="sublinkHeader" aria-disabled="true">Email Generation</li>
            <li class="sublink-item">
                <a class="sub-nav ps-3 rounded pb-3 pt-3" href="email_generated_soh.php">
                    <span class="sub-nav-text">Reports</span>
                </a>
            </li>
            <!-- <li class="sublink-item">
                <a class="sub-nav ps-3 rounded pb-3 pt-3" href="email_generated_joclosing.php">
                    <span class="sub-nav-text">JO Closing</span>
                </a>
            </li> -->
        </ul>
        <ul class="sublink-hover" id="sub-nav-hover2">
            <li class="sublink-item text-secondary" id="sublinkHeader" aria-disabled="true">Item Catalog</li>
            <li class="sublink-item">
                <a class="sub-nav ps-3 rounded pb-3 pt-3" href="item_catalog.php">
                    <span class="sub-nav-text">Masterlist</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- HOVER SUBLINK HOVER END-->



    <div class="menu-bar">
        <div class="menu">
            <ul class="menu-links ps-0">

                <li class="rounded" target="0" id="main-nav">
                    <a class="main-nav" href="/Glacier-Admintools/dashboard.php">
                        <i class='bx bx-home-alt icon'></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>


                    <li class="rounded" id="main-nav" target="1" data-bool="1">
                        <a class="main-nav position-relative">
                            <i class='bx bx-transfer-alt icon'></i>
                            <span class="text">Email Generation</span>
                            <i class='bx bx-chevron-right icon position-absolute top-20 end-0 pe-0' id="chevArrow"></i>
                        </a>
                    </li>

                <ul class="sublink ps-0" id="sub-nav1">
                    <li class="sublink-item">
                        <a class="sub-nav ps-3 rounded pb-3 pt-3" href="email_generated_soh.php">
                            <span class="sub-nav-text">Reports</span>
                        </a>
                    </li>
                    <!-- <li class="sublink-item">
                        <a class="sub-nav ps-3 rounded pb-3 pt-3" href="email_generated_joclosing.php">
                            <span class="sub-nav-text">Report List</span>
                        </a>
                    </li> -->
                </ul>
                <li class="rounded" id="main-nav" target="2" data-bool="2">
                    <a class="main-nav position-relative">
                        <i class='bx bxs-food-menu icon'></i>
                        <span class="text">Item Catalog</span>
                        <i class='bx bx-chevron-right icon position-absolute top-20 end-0' id="chevArrow"></i>
                    </a>
                </li>
                <ul class="sublink ps-0 pb-2" id="sub-nav2">
                    <li class="sublink-item">
                        <a class="sub-nav ps-3 rounded pb-3 pt-3" href="consultants.php">
                            <span class="sub-nav-text">Masterlist</span>
                        </a>
                    </li>
                </ul>
            </ul>
        </div>

        <div class="bottom-content">
            <li class="">
                <a href="app/Controller/logout.php">
                    <i class='bx bx-log-out icon'></i>
                    <span class="text">Logout</span>
                </a>
            </li>

            <li class="mode">
                <div class="sun-moon">
                    <i class='bx bx-moon icon moon'></i>
                    <i class='bx bx-sun icon sun'></i>
                </div>
                <span class="mode-text text">Dark mode</span>

                <div class="toggle-switch">
                    <span class="switch"></span>
                </div>
            </li>

        </div>
    </div>
</nav>