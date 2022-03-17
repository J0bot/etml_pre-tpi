const RED = 5;
const GREEN = 4;
const BLUE = 3;


var red_led = document.getElementById("RED_LED");
red_led.onchange = function()
{
    var red_led_value = document.getElementById("RED_LED").value;
    setLed(RED,red_led_value);
    console.log("Red : " + red_led_value);
}

var green_led = document.getElementById("GREEN_LED");
green_led.onchange = function()
{
    var green_led_value = document.getElementById("GREEN_LED").value;
    setLed(GREEN,green_led_value);
    console.log( "Green : "  + green_led_value);
}

var blue_led = document.getElementById("BLUE_LED");
blue_led.onchange = function()
{
    var blue_led_value = document.getElementById("BLUE_LED").value;
    setLed(BLUE,blue_led_value);
    console.log("Blue : " + blue_led_value);
}

async function setLed(led,intensity=255) {
    let url = "http://172.16.0.5/analog/"+ led + "/" + intensity;
    try {
        let res = await fetch(url);

        return await res.json();
    } catch (error) {
        console.log(error);
    }
}


function getButton(button) {
    const url = "http://172.16.0.5/digital/"+ button;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log(data.return_value);
            document.getElementById("button1_info").value = parseInt(data.return_value);
            return parseInt(data.return_value);
        })
        .catch(console.error)
}


/*
setLed(RED,document.getElementById("RED_LED").value);
setLed(GREEN,document.getElementById("GREEN_LED").value);
setLed(BLUE,document.getElementById("BLUE_LED").value );
*/