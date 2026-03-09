<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">

        <div class="slide" style="background-image: url('img/hutan.jpg');">
            <h3>Hutan</h3>
        </div>

        <div class="slide" style="background-image: url('img/pulau.jpg');">
            <h3>Pulau</h3>
        </div>

        <div class="slide" style="background-image: url('img/pantai.jpg');">
            <h3>Pantai</h3>
        </div>

        <div class="slide" style="background-image: url('img/gunung.jpeg');">
            <h3>Gunung</h3>
        </div>

        <div class="slide" style="background-image: url('img/persawahan.jpg');">
            <h3>Persawahan</h3>
        </div>

    </div>

    <script>
        activeslideimg();

        function activeslideimg(activeSlide = 2) {
            const slides = document.querySelectorAll(".slide");

            slides[activeSlide].classList.add("active");

            for (const slide of slides) {
                slide.addEventListener("click", () => {
                    clearActiveClasses();

                    slide.classList.add("active");
                });
            }


            function clearActiveClasses() {
                slides.forEach((slide) => {
                    slide.classList.remove("active");
                })
            }


        }
    </script>

</body>

</html>