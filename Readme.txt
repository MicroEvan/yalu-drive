Yalu - Easy Transfer across your network

Background:
Yaluna Drive is your single page app created for fun. You might wonder why I came up with it.
Well, one night while working on something(code obviously) on my pc, i needed to send something from my phone to my computer.
Bluetooth? send it to my computer via WhatsApp web?
All the options I had seemed like a lot to do, so I decided why not just make a web app and make a web app accessible 
to to devices on my network. I will be able to just move the files I want to send to my phone from my computer,
to a designated folder and then on my phone just navigate to mycomputerIpAddress/appname and be able to download OR upload the file


What I Consider doing:

Security-wise:

Validate file types and sizes during upload.
Restrict access to the uploads/ folder to ensure only authenticated users can upload/download files.

If you would like to run this program:

Local Network Setup:

Assign your PC a static IP on the local network to ensure accessibility, if you want to use your phone's hotspot you wont be able to
reserve an ip address, you will have to find out your pc ip address and use it.

I will explain in a separate write how you can setup your server, for now will use a command. 
So you can navigate to the root directory and run this command:
php -S your.ip.add.ress:port

replace port with 8000 or 80

on your phone, go to your.ip.add.ress/port/nameofapp



Future Enhancements:

Add user authentication.
Implement a database to track file details (optional).
Improve UI using my fav Tailwind CSS or Bootstrap.


Let me know if you enjoy this and you'd like help improving it!

(c) 2024
Techyaluna,