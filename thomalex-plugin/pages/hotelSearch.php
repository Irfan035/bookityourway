<?php
include 'hotelToken.php';
include('imagesUrls.php');
$CheckInDate_Jamaica = $_GET['check_in_date'] ? date("Y-m-d", strtotime($_GET['check_in_date'])) : date("Y-m-d");

$tomorrowUnix = strtotime("+2 day");
$CheckoutDate_Jamaica = $_GET['check_out_date'] ? date("Y-m-d", strtotime($_GET['check_out_date'])) : date("Y-m-d", $tomorrowUnix);

$checkInPlusOneUnix = strtotime($CheckoutDate_Jamaica);
$checkInPlusOneUnix = strtotime("-1 day", $checkInPlusOneUnix);
$checkInPlusOneUnix_destination = date("Y-m-d", $checkInPlusOneUnix);

$property = $atts['destination'];
echo '<input type="hidden" id="smoobu_single_property_id" value="' . $property . '">';
?>
<form action="" method="get">
    <?php
    echo '
            <div class="row shadow bg-light my-3 py-2 px-3 border border-5 border-white">
                <div class="col d-flex justify-content-center">
                    <input type="date" name="check_in_date" class="form-control" id="checkin_calander" onfocus="this.showPicker()" placeholder="Check In Date" aria-label="Check In Date" min="' . $CheckInDate_Jamaica . '" max="' . $checkInPlusOneUnix_destination . '" value="' . $CheckInDate_Jamaica . '" required>
                </div>
                <div class="col d-flex justify-content-center">
                    <input type="date" name="check_out_date" class="form-control" id="checkout_calander" onfocus="this.showPicker()" placeholder="Check Out Date" aria-label="Check Out Date" min="' . $CheckoutDate_Jamaica . '" value="' . $CheckoutDate_Jamaica . '" required>
                </div>
                <div class="col">
                    <input type="submit" class="button button-danger w-100" value="Search">
                </div>
            </div>
    ';
    ?>
</form>
<?php

// $HotelCityCode ;

// //Print it out
// echo $CheckoutDate;

// $Adults = 1;
// $RoomCount = 1;
// $PlaceId ="ChIJCzYy5IS16lQRQrfeQ5K5Oxw"; 

$destiation_url = 'https://rest.resvoyage.com/api/v1/hotel/references/destination/' . $property;
$response_DS = wp_remote_get(
    $destiation_url,
    array(
        'headers' => $headers_HS
    )
);
// $hotel_place_id = ''
$hotel_res = wp_remote_retrieve_body($response_DS);
$hotel_DS_res = json_decode($hotel_res, true);
//deCondition for PlaceId
$found = false;
foreach ($hotel_DS_res as $hotel_DS_name) {
    $DS_name = $hotel_DS_name['Name'];
    //echo $DS_name;
    if (strtolower($DS_name) == $property) {
        $found = true;
        $DS_placeID = $hotel_DS_name['PlaceId'];
        break; // Exit the loop once a match is found
    }
}

if (!$found) {
    echo "Destination Not there";
}

$url_HS = 'https://rest.resvoyage.com/api/v1/hotel/search';

$params_HS = array(
    'HotelCityCode' => '',
    'CheckInDate' => $CheckInDate_Jamaica,
    'CheckoutDate' => $CheckoutDate_Jamaica,
    'Adults' => '1',
    'RoomCount' => '1',
    'PlaceId' => $DS_placeID // Its the Jmaica Place
);
$request_url_HS = add_query_arg($params_HS, $url_HS);
// echo $request_url_HS . "<br><br>";
$response_HS = wp_remote_get(
    $request_url_HS,
    array(
        'timeout' => 25,
        'headers' => $headers_HS
    )
);

if (is_wp_error($response_HS)) {
    // Handle error
    $error_message = $response_HS->get_error_message();
    echo "Error: " . $error_message . "<br>";
} else {
    $response_HS_code = wp_remote_retrieve_response_code($response_HS);
    $response_HS_body = wp_remote_retrieve_body($response_HS);

    // Process the response based on the response code
    if ($response_HS_code == 200) {
        // Successful request
        $JmaicaHotels = json_decode($response_HS_body, true);
        // print_r($JmaicaHotels['SessionId']);
        $hotels = $JmaicaHotels['Hotels'];

        $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        // echo  $current_page . "<br>";
        $items_per_page = 10;
        $total_pages = ceil(count($hotels) / $items_per_page);
        // echo  $total_pages . "<br>";
        $start_index = ($current_page - 1) * $items_per_page;
        // echo  $start_index . "<br>";
        $current_page_hotels = array_slice($hotels, $start_index, $items_per_page);
        //  echo $current_page_hotels . "<br>";


        echo '<div id="JmaicaHotelsContent" class="paginatin">';
        foreach ($current_page_hotels as $hotel): ?>

            <div class="row shadow bg-light my-2 p-0">
                <div class="col-md-3 d-flex justify-content-center">
                    <img src="<?php echo $hotel['HotelMainImage'] ? $hotel['HotelMainImage'] : plugins_url('/../includes/img/hotel.png.gif', __FILE__); ?>"
                        class="img-fluid" alt="Jmaica">
                </div>
                <div class="col-md-6">
                    <div class="border-bottom px-2 py-3 capitalize">
                        <span>
                            <strong>
                                <?php echo $hotel['HotelName']; ?>
                            </strong>
                            <br>
                            <?php
                            $HRS = 0;
                            foreach ($hotel['HotelAwards'] as $award) {
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
                        </span>
                    </div>
                    <div class="border-bottom px-2 py-3">
                        <i class="fa fa-calendar"></i>
                        <?php echo date('m/d/y', strtotime($hotel['CheckInDate'])); ?>
                        <i class="fa fa-calendar" style="margin-left:1.5em"></i>
                        <?php echo date('m/d/y', strtotime($hotel['CheckOutDate'])); ?>
                    </div>
                    <div class="border-bottom px-2 py-3">
                        <i class="fa fa-map-marker"></i>
                        <span style="text-transform: capitalize">
                            <?php echo $hotel['HotelAddress']['StreetAddress'] . " " . $hotel['HotelAddress']['CityName']; ?>
                        </span>
                    </div>

                </div>
                <div class="col-md-3 border p-3 py-4 d-flex justify-content-center">
                    <div class="text-center">
                        <h2 style="line-height: 1.2; margin: 0;">
                            <?php echo "$ " . $hotel['DailyRatePerRoom']; ?>
                        </h2>
                        <span style="font-size:0.7em; line-height: 0.21;">
                            Price per night<br>
                            Taxes not included
                        </span>
                        <form action="/hoteldetails" method="get">
                            <input type="hidden" name="sessionId" value="<?= $JmaicaHotels['SessionId'] ?>">
                            <input type="hidden" name="hotelId" value="<?= $hotel['Id']; ?>">
                            <button class="hotelButton" type="submit" style="width: 100%">
                                Select
                            </button>
                        </form>

                        <a data-bs-toggle="collapse" href="<?php echo "#" . $hotel['HotelCode']; ?>" role="button"
                            aria-expanded="false" aria-controls="<?php echo $hotel['HotelCode']; ?>">
                            Details
                            <i class="dropdown-toggle"></i>
                        </a>

                    </div>
                </div>

                <div class="collapse card card-body m-3" id="<?php echo $hotel['HotelCode']; ?>">

                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                data-bs-target="<?php echo "#" . $hotel['HotelDescription']; ?>" type="button" role="tab"
                                aria-controls="<?php echo $hotel['HotelDescription']; ?>" aria-selected="true">Details</button>
                            <!-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Map View</button> -->
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="<?php echo $hotel['HotelDescription']; ?>" role="tabpanel"
                            aria-labelledby="<?php echo $hotel['HotelDescription']; ?>" tabindex="0">
                            <h3>Hotel amenities</h3>
                            <div class="hotel-ammet p-0">
                            <?php 
                                foreach ($hotel['HotelAmenities'] as $key => $value) {
                                    if($images_array[$key]){
                                        echo '<img class="mx-1" style="width:30px; height: 30px;"
                                            src="' . $images_array[$key] . '" title="' . $value . '">';
                                    }
                                }
                            ?>
                            </div>
                            <h3>Description</h3>
                            <p class="text-dark">
                                <?php echo $hotel['HotelDescription']['0']; ?>
                            </p>
                        </div>
                        <!-- <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d3784.1898003929145!2d-77.60496!3d18.47506!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sus!4v1687273104799!5m2!1sen!2sus"
                            width="auto" height="400" style="border:05;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div> -->
                    </div>

                </div>

            </div>

        <?php endforeach;
        // print_r($hotels);
        echo '</div>';
        $Previous = $current_page - 1;
        $Next = $current_page + 1;
        // ---------------------------------add pagination----------------------------------
        echo '<div>';
        echo '<nav aria-label="Page navigation">';
        echo '<ul id="jamaica_pagination" class="pagination justify-content-center">';

        // Display the previous page link
        if ($current_page > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=' . $Previous . '&check_in_date=' . $CheckInDate_Jamaica . '&check_out_date=' . $CheckoutDate_Jamaica . '" aria-label="Previous">
            <span aria-hidden="true">&laquo; Previous</span></a></li>';
        } else {
            echo '<li class="page-item disabled"><a class="page-link" href="?page=' . $Previous . '&check_in_date=' . $CheckInDate_Jamaica . '&check_out_date=' . $CheckoutDate_Jamaica . '"><span aria-hidden="true">&laquo; Previous</span></a></li></a></li>';
        }

        // Display the numbered page links
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i === $current_page) {
                echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '&check_in_date=' . $CheckInDate_Jamaica . '&check_out_date=' . $CheckoutDate_Jamaica . '">' . $i . '</a></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&check_in_date=' . $CheckInDate_Jamaica . '&check_out_date=' . $CheckoutDate_Jamaica . '" >' . $i . '</a></li>';
            }
        }

        // Display the next page link
        if ($current_page < $total_pages) {
            echo '<li class="page-item"><a class="page-link" href="?page=' . $Next . '&check_in_date=' . $CheckInDate_Jamaica . '&check_out_date=' . $CheckoutDate_Jamaica . '" aria-label="Next"><span aria-hidden="true">Next &raquo;</span></a></li>';

        } else {
            echo '<li class="page-item disabled"><a class="page-link" href="?page=' . $Next . '&check_in_date=' . $CheckInDate_Jamaica . '&check_out_date=' . $CheckoutDate_Jamaica . '"> <span aria-hidden="true">Next &raquo;</span></a></li>';
        }

        echo '</ul>';
        echo '</nav>';
        echo '</div>';

    } else {
        // Handle non-200 response
        echo '<div class="bg-light shadow text-center p-5 border border-5 border-white">
            <p class="my-5">Hotel information not available.</p>

        </div>';
    }
}

?>
<script>
    jQuery(document).ready(function ($) {
        jQuery('#checkout_calander').change(function () {
            var date = $(this).val();
            console.log(date, 'change');
            jQuery("#checkin_calander").attr({
                "max": date
            });
        });
    });

</script>