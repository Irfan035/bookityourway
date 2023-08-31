<?php
include 'hotelToken.php';
include('imagesUrls.php');

$qs = explode("?", $_SERVER['REQUEST_URI']);
$phpFileName = basename($qs[0]);
// echo $phpFileName;
if ($phpFileName === 'hoteldetails') {
    $SessionId = $_GET['sessionId'];
    $HotelId = $_GET['hotelId'];
    $url_HD = 'https://rest.resvoyage.com/api/v1/hotel/details';

    $params_HD = array(
        'SessionId' => $SessionId,
        'HotelId' => $HotelId
    );
    $request_url_HD = add_query_arg($params_HD, $url_HD);

    $response_HD = wp_remote_get(
        $request_url_HD,
        array(
            'timeout' => 25,
            'headers' => $headers_HS
        )
    );
    if (is_wp_error($response_HD)) {
        // Handle error
        $error_message = $response_HD->get_error_message();
        echo "Error: " . $error_message . "<br>";
    } else {
        $response_body_HD = wp_remote_retrieve_body($response_HD);
        $hotels_details = json_decode($response_body_HD, true);
        if (is_array($hotels_details)) {
            $S_hotel_D = $hotels_details['Hotels'];
            // print_r($S_hotel_D);
            foreach ($S_hotel_D as $hotel_SD): ?>

                <div class="row shadow bg-light my-2 p-0">
                    <div class="col-md-5 border">
                    <style>
                        .carousel-indicators [data-bs-target] {
                            background-color: transparent;
                            border: 3px solid;
                            }

                        .carousel-indicators {
                            margin: 0;
                            position: relative;
                            flex-wrap: wrap;
                            align-content: flex-end;
                            justify-content: center;
                            }

                        .carousel-indicators button,
                        .carousel-indicators button.active {
                            width: auto !important;
                            height: 30px !important;
                            border: 3px solid #FF060 !important;
                            margin: 2px;

                            }

                        .carousel-indicators img {
                            width: 30px !important;
                            height: 30px !important;
                            }
                        .carousel-control-prev-icon, .carousel-control-next-icon{
                            color: #FF060;
                        }
                    </style>
                        <!-- Carousel wrapper -->
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                           <div class="carousel-inner">
                                <div class="carousel-item pt-3 pb-1 px-5 active">
                                    <img src="<?php echo $hotel_SD['HotelMainImage'] ? $hotel_SD['HotelMainImage'] : plugins_url('/../includes/img/hotel.png.gif', __FILE__); ?>"
                                        class="d-block w-100 img-fluid" alt="..." style="width: auto; height:300px">
                                </div>
                                <?php
                                $HSD_images = $hotel_SD['HotelImages'];
                                foreach ($HSD_images as $HSD_img) {
                                    ?>
                                    <div class="carousel-item pt-3 pb-1 px-5">
                                        <img src="<?php echo $HSD_img; ?>" class="d-block w-100 img-fluid" alt="..."
                                            style="width: auto; height:300px">
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                            <div class="carousel-indicators">
                                <button data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="col-2 active">
                                    <img src="<?php echo $hotel_SD['HotelMainImage'] ? $hotel_SD['HotelMainImage'] : plugins_url('/../includes/img/hotel.png.gif', __FILE__); ?>"
                                        class="d-block" alt="..." >
                                </button>
                                <?php
                                $HSD_images = $hotel_SD['HotelImages'];
                                $counter_img = 0;
                                foreach ($HSD_images as $HSD_img) {
                                    $counter_img++; // Increment counter
                                    ?>
                                    <button data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $counter_img; ?>" class="col-2">
                                        <img src="<?php echo $HSD_img; ?>" class="d-block" alt="...">
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-7 border p-0">
                        <div class="border-bottom text-capitalize">
                            <div class="mx-3 mt-3 my-2">
                                <strong class="fs-6 text-capitalize">
                                    <?php echo $hotel_SD['HotelName']; ?>
                                </strong>
                                <br>
                                <?php
                                $HRS = 0;
                                foreach ($hotel_SD['HotelAwards'] as $award) {
                                    if ($award['Provider'] == 'Local Star Rating' || $award['Provider'] == 'Unspecified' || $award['Provider'] != 'OHG') {
                                        $HRS = $award['Rating'];
                                    }
                                }
                                if ($HRS) {
                                    for ($i = 0; $i < $HRS; $i++) {
                                        echo '<i class="fa fa-star" style="font-size:0.8em;"></i>';
                                    }
                                } else {
                                    echo '<span style="font-size:0.7em;">(No Star Rating)</span>';
                                }
                                ?>

                            </div>
                        </div>
                        <div class="border-bottom">
                            <p class="mx-3 my-2 fs-6">
                                <i class="fa fa-calendar"></i>
                                <?php echo date('m/d/y', strtotime($hotel_SD['CheckInDate'])); ?>
                                <i class="fa fa-calendar" style="margin-left:1.5em"></i>
                                <?php echo date('m/d/y', strtotime($hotel_SD['CheckOutDate'])); ?>

                            </p>
                        </div>

                        <div class="border-bottom">
                            <p class="mx-3 my-2 fs-6">
                                <i class="fa fa-phone"></i>
                                <?php echo $hotel_SD['HotelPhone']; ?>
                                <i class="fa fa-fax" style="margin-left:1.5em"></i>
                                <?php echo $hotel_SD['HotelFax']; ?>
                            </p>
                        </div>
                        <div class="border-bottom">
                            <div class="hotel-ammet py-2 px-3">
                                <?php
                                foreach ($hotel_SD['HotelAmenities'] as $key => $value) {
                                    if ($images_array[$key]) {
                                        echo '<img class="mx-1" style="width:30px; height: 30px;"
                                                src="' . $images_array[$key] . '" title="' . $value . '">';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="">
                            <p class="mx-3 my-2">
                                <i class="fa fa-map-marker"></i>
                                <span class="fs-6">
                                    <?php echo $hotel_SD['HotelAddress']['StreetAddress'] . " " . $hotel_SD['HotelAddress']['CityName']; ?>
                                </span><br><br>
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d3784.1898003929145!2d-77.60496!3d18.47506!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1687273104799!5m2!1sen!2sus"
                                    width="auto" style="border:05;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Room Data -->
                <?php
                $SH_rooms = $hotel_SD['Rooms'];
                foreach ($SH_rooms as $SH_roomTypes):
                    ?>


                    <div class="row my-2 bg-light p-0 shadow">
                        <div class="col-md-9 p-0 border">
                            <a class="text-dark" data-bs-toggle="collapse" href="<?php echo "#" . $SH_roomTypes['Id'] ?>" role="button"
                                aria-expanded="false" aria-controls="<?php echo $SH_roomTypes['Id'] ?>">
                                <div class="border-bottom">
                                    <p class="m-3 fs-6"><strong>Category: </strong>
                                        <?php echo $SH_roomTypes['Category']; ?>
                                        <strong> Number of beds: </strong>
                                        <?php echo $SH_roomTypes['NumberOfBeds']; ?>
                                        <strong> Bed type: </strong>
                                        <?php echo $SH_roomTypes['BedType']; ?>
                                    </p>
                                </div>
                                <div class="border-bottom">

                                    <p class="m-3 fs-6"><strong>Cancellation policy: </strong>
                                        <span class="">
                                            <?php echo "No cancellation fee if cancelled before " . date('m/d/y', strtotime($hotel_SD['CheckInDate'])) . " at 03:00 PM(Local time)"; ?>
                                        </span><br>
                                        <strong> Guarantee Type: </strong>
                                        <?php echo $SH_roomTypes['GuaranteeType']; ?>
                                    </p>
                                </div>
                                <div class="p-3 fs-6">
                                    <p><strong>Description: </strong>
                                        <?php foreach ($SH_roomTypes['RoomText'] as $SH_RDes) {
                                            echo $SH_RDes . "<br>";
                                        }
                                        ?>
                                    </p>
                                </div>

                            </a>
                        </div>
                        <div class="col-md-3 border p-3 py-5 d-flex justify-content-center">
                            <div class="text-center">
                                <h2 style="line-height: 1.2; margin: 0;">
                                    <?php echo "$ " . $SH_roomTypes['RoomRate']; ?>
                                </h2>
                                <span style="font-size:0.7em; line-height: 0.21;">
                                    <?php
                                    $SH_CheckInDate = date('y-m-d', strtotime($hotel_SD['CheckInDate']));
                                    $SH_CheckOutDate = date('y-m-d', strtotime($hotel_SD['CheckOutDate']));
                                    $SH_CheckInDate_dc = date_create($SH_CheckInDate);
                                    $SH_CheckOutDate_dc = date_create($SH_CheckOutDate);

                                    $dateDiff = date_diff($SH_CheckInDate_dc, $SH_CheckOutDate_dc);
                                    $DH_dateDiff = $dateDiff->format("%a");

                                    echo "Total cost for " . $DH_dateDiff . " night(s) for " . $SH_roomTypes['NumberOfRooms'] . "  room(s)"; ?>
                                </span>

                                <button class="hotelButton" type="">
                                    Book Now
                                </button><br>
                                <br>
                                <a class="colorBtn fs-6" data-bs-toggle="collapse" href="<?php echo "#" . $SH_roomTypes['Id'] ?>"
                                    role="button" aria-expanded="false" aria-controls="<?php echo $SH_roomTypes['Id'] ?>">
                                    See pricing details
                                    <i class="dropdown-toggle"></i>
                                </a>
                            </div>
                        </div>
                        <div class="collapse p-0" id="<?php echo $SH_roomTypes['Id'] ?>">
                            <div class="card card-body">
                                <span class="fs-6">
                                    <strong>Taxes Total:</strong>
                                    <?php echo '$ ' . $SH_roomTypes['TotalTaxes']; ?><br>

                                    <strong> Rate Code:</strong>
                                    <?php echo $SH_roomTypes['RatePlanCode']; ?><br>

                                    <strong>Room Type Code:</strong>
                                    <?php echo $SH_roomTypes['RoomTypeCode']; ?><br>

                                    <strong>Daily Rates Taxes Not Included:</strong>
                                <div class="table-responsive-md">
                                    <table class="table">
                                        <?php
                                        // Date Format: Y/m/dd
                                        $start_date = $SH_CheckInDate;
                                        ?><tr><?php
                                        foreach ($SH_roomTypes['RoomDailyrate'] as $key => $value) {
                                            ?>
                                                <th>
                                                    <?php echo date('D', strtotime($start_date . ' +' . $key . ' day')); ?>
                                                </th>
                                            <?php

                                        }?>
                                        </tr><tr><?php
                                        foreach ($SH_roomTypes['RoomDailyrate'] as $key => $value) {
                                            ?>
                                            
                                                <td>
                                                    <?php echo '$ ' . $value['RoomDailyRate']; ?>
                                                </td>
                                            <?php

                                        }
                                        ?>
                                        </tr>
                                    </table>
                               
                                </div>
                                </span>
                            </div>
                        </div>
                    </div>


                    <?php
                endforeach;

            endforeach;

        } else {
            echo "Invalid response from API";
        }
    }
} else {
    echo "Action URL is wrong or parameter is missing";
}
?>