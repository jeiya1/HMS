<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standard Rooms</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/output.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body>
    <?php require_once __DIR__ . '/../components/header.view.php'; ?>

    <div class="py-10 px-30 flex flex-col gap-5">
        <div class="justify-start text-black text-lg font-normal font-crimson">Home > Book Now</div>

        <div class="flex justify-between gap-8">
            <div class="p-6 w-2/3 bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)]">
                <div class="justify-center text-black text-3xl font-normal font-crimson">Standard Room</div>
                <div class="flex items-center justify-start">
                    <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                    <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                    <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                    <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                    <img src="/assets/icons/star.svg" alt="star" class="w-4 h-4">
                    <h6 class="font-normal font-roboto ml-3">1 Review</h6>
                </div>
                <br>
                <div class="flex gap-6">
                    <div class="w-[20%] flex flex-col gap-4 left-thumbnails">
                        <img class="w-50 h-1/3 rounded-[3px] object-cover  hover:scale-105 transition-transform duration-300"
                            src="/assets/images/standard.jpg" />
                        <img class="w-50 h-1/3 rounded-[3px] object-cover hover:scale-105 transition-transform duration-300"
                            src="/assets/images/g3.jpg" />
                        <img class="w-50 h-1/3 rounded-[3px] object-cover hover:scale-105 transition-transform duration-300"
                            src="/assets/images/g7.jpg" />
                    </div>

                    <div class="w-[80%] main-image">
                        <img class="w-full max-h-150 object-cover rounded" src="/assets/images/standard.jpg" />
                    </div>
                </div>
            </div>
            <form action="/reservation" method="POST"
                class="w-1/3 bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] p-6 flex flex-col gap-6">
                <div class="justify-center text-black text-xl font-normal font-crimson">Check In - Check Out</div>
                <input type="text" name="checkin" id="daterange" placeholder="Check-In — Check-Out"
                    class="bg-white rounded-sm p-2 text-crimson-600 font-crimson border border-gray-300">
                <div class="justify-center text-black text-xl font-normal font-crimson">Guests</div>
                <input type="number" id="guests" placeholder="Guests" min="1" value="1"
                    class="bg-white rounded-sm p-2 text-crimson-600 font-crimson border border-gray-300">
                <div class="justify-center text-black text-xl font-normal font-crimson">Room Type</div>
                <div class="flex items-center gap-4">
                    <label class="relative flex items-center cursor-pointer">
                        <input type="radio" name="room" value="single" data-base-price="1800" class="peer sr-only"
                            checked required>
                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full shrink-0
                    peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                        <span class="ml-2 text-gray-700 font-roboto">Single</span>
                    </label>

                    <label class="relative flex items-center cursor-pointer">
                        <input type="radio" name="room" data-base-price="2700" value="double" class="peer sr-only">
                        <div class="w-5 h-5 border-2 border-gray-400 rounded-full shrink-0
                    peer-checked:border-[#c39c4d] peer-checked:bg-[#c39c4d] transition-all"></div>
                        <span class="ml-2 text-gray-700 font-roboto">Double</span>
                    </label>
                </div>
                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
                <div class="justify-center text-zinc-500 text-xl font-normal font-crimson">Room Price</div>
                <div id="room-price" class="justify-center text-black text-xl font-normal font-crimson">₱1,800</div>
                <div class="h-0.5 w-full bg-linear-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>
                <div class="justify-center text-zinc-500 text-xl font-normal font-crimson">Total</div>
                <div id="total-price" class="justify-center text-black text-xl font-normal font-crimson">₱2,016</div>
                <button type="submit" class="text-white font-roboto text-[16px] font-semibold leading-normal rounded-sm bg-[#c39c4d] p-3 
           hover:bg-[#b28a44] transition-colors duration-300">
                    BOOK NOW
                </button>
            </form>
        </div>

        <div class="pt-2">
            <div class="ml-10">
                <h1 class="justify-center text-black text-2xl font-normal font-crimson">Room Information</h1>
                <!-- TODO: ADDITIONAL: reviews -->
                <!-- <h1>Reviews</h1> -->
                <div class="h-1 w-50 bg-gradient-to-r from-yellow-100 to-yellow-800 rounded-lg"></div>

            </div>
            <div class="w-full bg-white rounded shadow-[0px_0px_4px_0px_rgba(0,0,0,0.25)] p-6 flex flex-col gap-4">
                <div class="justify-center text-black text-md font-roboto font-normal">Enjoy comfort and simplicity
                    in our Standard Room, designed for a relaxing stay. Featuring cozy bedding, modern furnishings, and
                    essential amenities, this room provides everything you need for a pleasant and convenient
                    experience. Perfect for solo travelers or couples looking for a comfortable space to unwind after a
                    day of exploring.</div>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">MAX NO. GUESTS</div>
                <ul class="text-black text-md font-normal font-roboto">
                    <li>Single – 2 Guests</li>
                    <li>Double – 3 Guests</li>
                </ul>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">CHECK-IN AND CHECK-OUT TIME
                    <ul class="text-black text-md font-normal font-roboto">
                        <li>Check-in: 12:00 PM</li>
                        <li>Check-out: 11:00 AM</li>
                    </ul>
                </div>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">BED TYPES</div>
                <ul class="text-black text-md font-normal font-roboto">
                    <li>2 * Single Bed</li>
                </ul>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">ROOM FEATURES</div>
                <ul class="text-black text-md font-normal font-roboto">
                    <li class="flex items-center space-x-2">
                        <img src="/assets/icons/wifi.svg" alt="Wi-Fi Icon" class="w-5 h-5">
                        <span>Wi-Fi</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <img src="/assets/icons/AC.svg" alt="Air Condition Icon" class="w-5 h-5">
                        <span>Air Condition</span>
                    </li>
                </ul>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">HOTEL DESCRIPTION</div>
                <div class="justify-center text-black text-2xl font-roboto font-bold">Welcome to Subic Riviera!</div>
                <div class="justify-center text-black text-lg font-roboto font-normal">Experience elegance, comfort, and
                    seaside relaxation at Subic Riviera. Located near the beautiful coastline of Subic, our hotel offers
                    a perfect escape for travelers looking to unwind while enjoying modern luxury. Whether you are
                    visiting for leisure, business, or a special event, Subic Riviera provides a memorable stay with
                    exceptional service, stylish accommodations, and premium facilities. </div>
                <div class="justify-center text-black text-lg font-roboto font-semibold">Benefits of Staying at Riviera!
                </div>
                <div class="justify-center text-black text-md font-roboto italic">Accommodation:</div>
                <div class="justify-center text-black text-md font-roboto">Relax in our well-designed rooms and suites
                    that combine comfort with modern elegance. Each room is thoughtfully furnished with plush bedding,
                    individually controlled air conditioning, high-speed Wi-Fi, a smart TV, and spacious interiors that
                    create a calm and relaxing atmosphere. After a day of exploring Subic or attending meetings, our
                    rooms provide the perfect place to rest and recharge.</div>
                <div class="justify-center text-black text-md font-roboto italic">Dining:</div>
                <div class="justify-center text-black text-md font-roboto">Enjoy a unique dining experience at our
                    rooftop bar and dining area, where guests can savor a variety of refreshing drinks and delicious
                    dishes while overlooking scenic views. Our menu offers a selection of local and international
                    flavors, perfect for casual meals, sunset drinks, or a relaxing evening with friends and family.
                </div>
                <div class="justify-center text-black text-md font-roboto italic">Facilities:</div>
                <div class="justify-center text-black text-md font-roboto">Subic Riviera offers a variety of facilities
                    designed to enhance your stay. Take a refreshing swim in our outdoor swimming pool, enjoy stunning
                    views while relaxing at the rooftop pool and bar, or stay active in our fully equipped fitness gym.
                    Guests can also enjoy comfortable lounge areas and beautifully designed spaces perfect for
                    relaxation.</div>
                <div class="justify-center text-black text-md font-roboto italic">Events and Meetings:</div>
                <div class="justify-center text-black text-md font-roboto">Host memorable events at our spacious and
                    versatile event halls. Whether you are planning a corporate meeting, conference, wedding reception,
                    or private celebration, our event venues provide the ideal setting. Equipped with modern audiovisual
                    technology and supported by our dedicated event staff, we ensure every event is seamless and
                    successful.</div>
                <div class="justify-center text-black text-md font-roboto italic">Location:</div>
                <div class="justify-center text-black text-md font-roboto">Situated close to the sea, Subic Riviera
                    allows guests to enjoy the refreshing ocean breeze and easy access to nearby coastal attractions.
                    Guests can explore the nearby shoreline, enjoy beautiful sunsets, and visit shopping, dining, and
                    entertainment spots within the Subic area.</div>
                <div class="justify-center text-black text-md font-roboto italic">Exceptional Service:</div>
                <div class="justify-center text-black text-md font-roboto">At Subic Riviera, we pride ourselves on
                    delivering exceptional service. From check-in to check-out, our friendly and professional staff are
                    dedicated to ensuring your stay is comfortable, convenient, and truly memorable.</div>
                <br>
                <div class="justify-center text-zinc-500 text-md font-roboto font-normal">HOTEL POLICIES</div>
                <ul class="list-disc text-black text-md font-normal font-roboto ml-5">
                    <li>Accommodation will only be provided to guests whose details are registered at the hotel front
                        desk.</li>
                    <li>Guests must present a valid photo identification during check-in.</li>
                    <li>Applicable taxes and government charges may apply.</li>
                    <li>Full or advance payment may be required upon check-in.</li>
                    <li>Standard check-in time is 0:00 PM and check-out time is 0:00 AM. Early check-in and late
                        check-out are subject to availability.</li>
                    <li>The hotel reserves the right to refuse accommodation to guests who do not comply with hotel
                        policies and regulations.</li>
                    <li>Guests are responsible for any damage or loss caused to hotel property during their stay.</li>
                </ul>
            </div>
        </div>
    </div>
    <script src="/js/thumbnail.js"></script>
    <script src="/js/daterange.js"></script>
    <script src="/js/calculateTotalAmount.js"></script>
</body>

</html>