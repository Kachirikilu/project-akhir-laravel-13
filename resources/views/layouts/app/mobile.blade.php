<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mobile</title>
    @vite(['resources/css/color-template.css', 'resources/css/color-template-special.css', 'resources/css/app.css', 'resources/js/app.js',
                'resources/js/User.js', 'resources/js/ProgramStudi.js', 'resources/js/MataKuliah.js',
                'resources/js/RPS.js', 'resources/js/CPMK.js', 'resources/js/SubCPMK.js', 'resources/js/CPL.js', 'resources/js/Referensi.js', 'resources/js/TimDosen.js',
                'resources/js/Kelas.js', 'resources/js/KelasJadwal.js', 'resources/js/KelasSesi.js', 'resources/js/Nilai.js', 'resources/js/NilaiPeriode.js',
                'resources/js/ThemeManager.js'])

</head>

<body id="background" class="mobileBody" onload="startTimeMobileClock()">


    <div id="diviPhone" style="display: flex;" class="diviPhone">

        <main class="WadahKalkulatorHP">
            <div class="layerKalkulatorOnHPMobile" id="layarHP" style="display: inline;">

                <div id="poni">
                    <div id="poniX"><a href="/" style="text-decoration: none; color: white;">Back to home</a></div>
                </div>

                <button form="" id="lonceng"></button>
                <button form="" id="volUp"></button>
                <button form="" id="volDown"></button>
                <button form="" onclick="onHP()" id="on-off"></button>

                <div class="poni">
                    <div id="poniRS1" class="poniR1">OO</div>
                </div>
                <div class="poni">
                    <div id="poniRS2" class="poniR1X">00</div>
                </div>
                <div class="poni">
                    <div id="poniRS3" class="poniR2">O</div>
                </div>
                <div class="poni">
                    <div id="poniRS4" class="poniR2X">0</div>
                </div>

                <div class="navMobile">
                    <div style="display: flex; gap: 5.2px">
                        <div class="navMobileLeft" id="clockNavMobile"></div>
                        <div class="navMobileLeft" id="dayNavMobile"></div>
                    </div>
                    <div class="navMobileRight">
                        <ion-icon name="cellular" class="navSignal"></ion-icon>
                        <ion-icon name="wifi" class="navWiFi"></ion-icon>
                        <ion-icon name="battery-full"></ion-icon>
                    </div>
                </div>


                <div class="iFrameDiv" style="overflow: hidden;">
                    <div class="iFrameMobileTop">
                        <div class="inputiFrameDiv">
                            <ion-icon name="lock-closed" class="inputLock"></ion-icon>
                            <input type="text" class="inputiFrame" id="inputSearch" value="http://localhost:8080" onchange="searchButton()">
                            <button class="inputCopyButton" onclick="copylink()"><ion-icon name="open-outline" class="inputCopy"></ion-icon></button>
                        </div>
                    </div>
                    <div class="iFrameDivX"><iframe src="http://localhost:8080" class="iFrameMobile" id="iFrame"></iframe></div>
                    <div class="footerUnder">
                        <div class="iconFooterDiv">
                            <ion-icon name="arrow-back-outline" class="iconFooter" style="transform: scale(1.2);"></ion-icon>
                            <ion-icon name="arrow-forward" class="iconFooter" style="transform: scale(1.2);"></ion-icon>
                            <div class="addIconFooter"><ion-icon name="add" class="iconFooter" style="transform: scale(1.5);"></ion-icon></div>
                            <div class="iconFooterX">
                                <p style="transform: translateX(-0.62px);">14</p>
                            </div>
                            <ion-icon name="ellipsis-horizontal" class="iconFooter" style="transform: scale(1.2);"></ion-icon>
                        </div>
                    </div>
                </div>

                <div class="footerMobile">
                    <button onclick="homeButton()" class="gesturiPhone"></button>
                </div>

                <div class="iFrameMobileBottom"></div>

                <script type="text/javascript">
                    let iFrame = document.getElementById("iFrame");
                    let search = document.getElementById("inputSearch");

                    function searchButton() {
                        iFrame.src = search.value;
                    }

                    function homeButton() {
                        iFrame.src = "http://localhost:8080/";
                        search.value = "http://localhost:8080/";
                    }

                    function startTimeMobileClock() {
                        const date = new Date();
                        let d = date.getDay();
                        let h = date.getHours();
                        let m = date.getMinutes();
                        if (h < 10) {
                            if (m < 10) {
                                document.getElementById("clockNavMobile").innerHTML = "0" + h + ".0" + m
                            } else if (m > 9) {
                                document.getElementById("clockNavMobile").innerHTML = "0" + h + "." + m
                            }
                        } else if (h > 9) {
                            if (m < 10) {
                                document.getElementById("clockNavMobile").innerHTML = h + ".0" + m
                            } else if (m > 9) {
                                document.getElementById("clockNavMobile").innerHTML = h + "." + m
                            }
                        }

                        if (d == 0) {
                            document.getElementById("dayNavMobile").innerHTML = "Sun"
                        } else if (d == 1) {
                            document.getElementById("dayNavMobile").innerHTML = "Mon"
                        } else if (d == 2) {
                            document.getElementById("dayNavMobile").innerHTML = "Tue"
                        } else if (d == 3) {
                            document.getElementById("dayNavMobile").innerHTML = "Wed"
                        } else if (d == 4) {
                            document.getElementById("dayNavMobile").innerHTML = "Thu"
                        } else if (d == 5) {
                            document.getElementById("dayNavMobile").innerHTML = "Fri"
                        } else if (d == 6) {
                            document.getElementById("dayNavMobile").innerHTML = "Sat"
                        }

                        setTimeout(function() {
                            startTimeMobileClock()
                        }, 1000);
                    }

                    function copylink() {
                        search.select();
                        search.setSelectionRange(0, 99999);

                        navigator.clipboard.writeText(search.value);
                    }
                </script>

            </div>
            <form class="layerKalkulatorHP" id="layaroffHP" style="display: none;">

                <div id="poni">
                    <div class="poniR1">OO</div>
                </div>
                <div id="poni">
                    <div class="poniR1X">00</div>
                </div>
                <div id="poni">
                    <div class="poniR2">O</div>
                </div>
                <div id="poni">
                    <div class="poniR2X">0</div>
                </div>

                <button form="" id="lonceng"></button>
                <button form="" id="volUp"></button>
                <button form="" id="volDown"></button>
                <button form="" onclick="onHP()" id="on-off"></button>

            </form>

            <div class="bottomPhone">
                <button id="gesturiPhone" onclick="homeButton()" class="gesturiPhone"></button>

                <div class="bottomPhoneRL">
                    <button form="" id="antena"></button>
                    <div class="bottomSpeakerL">
                        <div id="speaker1"></div>
                        <div id="speaker2"></div>
                        <div id="speaker3"></div>
                    </div>
                </div>

                <button form="" id="baut1"></button>
                <button form="" id="charger"></button>
                <button form="" id="baut2"></button>

                <div class="bottomPhoneRL">
                    <div class="bottomSpeakerR">
                        <div id="speaker4"></div>
                        <div id="speaker5"></div>
                        <div id="speaker6"></div>
                        <div id="speaker7"></div>
                        <div id="speaker8"></div>
                    </div>
                </div>
            </div>

        </main>

    </div>


    <script src="/CSS & JS/kalkulator.js?v=<?php echo time(); ?>" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>