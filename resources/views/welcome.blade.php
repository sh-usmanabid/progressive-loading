<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progressive Loading</title>
    
    <style>
        body {
            padding: 0 !important;
            margin: 0 !important;
        }

        .container {
            width: 100%;
            height: 100%;
        }

        .blur-load {
            position: relative; 
            width: 100vw;
            height: 100vh;
            background-size: cover;
            background-position: center;
        }

        .blur-load img {
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 300ms ease-in-out, filter 300ms ease-in-out;
            filter: blur(10px);
        }

        .blur-load.loaded img {
            opacity: 1;
            filter: blur(0);
        }

        .blur-load::before {
            content: "";
            position: absolute;
            inset: 0;
            animation: pulse 2.5s infinite;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .blur-load.loaded::before {
            content: none;
            animation: pulse 2.5s infinite;
        }
        
        @keyframes pulse {
            0% {
                background-color: rgba(255, 255, 255, 0);
            }
            50% {
                background-color: rgba(255, 255, 255, 0.1);
            }
            100% {
                background-color: rgba(255, 255, 255, 0);
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="blur-load">
            <img src="{{ asset('storage/images/1.jpg') }}" alt="" loading="lazy">
        </div>
        <div class="blur-load">
            <img src="{{ asset('storage/images/2.jpg') }}" alt="" loading="lazy">
        </div>
        <div class="blur-load">
            <img src="{{ asset('storage/images/3.jpg') }}" alt="" loading="lazy">
        </div>
    </div>

    <script>
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target.querySelector("img");
                    if (img) {
                        const filename = img.getAttribute('data-src').split('/').pop();
                        const lowResSrc = `/image/low-res/${filename}`;

                        fetch(lowResSrc)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                const actualLowResUrl = data.split('/').pop();
                                entry.target.style.backgroundImage = `url('storage/images/${actualLowResUrl}')`;
                                img.src = img.getAttribute('data-src');

                                img.addEventListener("load", () => {
                                    entry.target.classList.add("loaded");
                                    observer.unobserve(entry.target);
                                });
                            })
                            .catch(error => {
                                console.error('Error fetching low-res image:', error);
                            });
                    }
                }
            });
        });

        const blurDivs = document.querySelectorAll(".blur-load");

        blurDivs.forEach(div => {
            const img = div.querySelector("img");
            if (img) {
                img.dataset.src = img.getAttribute('src');
                img.removeAttribute('src');
                observer.observe(div);
            }
        });
    </script>
</body>

</html>