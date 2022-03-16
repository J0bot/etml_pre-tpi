const RED = 5;
const GREEN = 4;
const BLUE = 3;
var RED_int ;
var GREEN_int ;
var BLUE_int ;


async function setLed(led,intensity=255) {
    let url = "http://172.16.0.5/analog/"+ led + "/" + intensity;
    try {
        let res = await fetch(url);
        return await res.json();
    } catch (error) {
        console.log(error);
    }
}
/*
setLed(RED,RED_int);
setLed(GREEN,GREEN_int);
setLed(BLUE,BLUE_int);
*/