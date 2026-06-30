<?php
$first_name = ["Mark", "Vince", "Ken", "Yuan", "Chad", "Jako", "Andrew", "Jhon", "Miguel", "Calvin"]
$last_name = ["Piosan", "Hanana", "Bacobod", "Crutz", "Sunicon", "Marinez", "Chanz", "Quejo", "Tagaltag", "Bacosta"]
$middle_initials = range('A', 'Z');

$courses = [
    "BSCS" => [
        "name" => "Bachelor of Science in Computer Science",
        "subjects" => [
            ["code" => "CC101", "name" => "Programming Fundamentals",       "units" => 3],
            ["code" => "CC102", "name" => "Object-Oriented Programming",     "units" => 3],
            ["code" => "CC103", "name" => "Data Structures and Algorithms",  "units" => 3],
            ["code" => "CC104", "name" => "Database Systems",                "units" => 3],
            ["code" => "CC105", "name" => "Web Development",                 "units" => 3],
        ]
    ],

    "BSIT" => [
        "name" => "Bachelor of Science in Information Technology",
        "subjects" => [
            ["code" => "IT101", "name" => "Computer Fundamentals",           "units" => 3],
            ["code" => "IT102", "name" => "Networking 1",                    "units" => 3],
            ["code" => "IT103", "name" => "Systems Analysis and Design",     "units" => 3],
            ["code" => "IT104", "name" => "Information Assurance",           "units" => 3],
            ["code" => "IT105", "name" => "Web Technologies",                "units" => 3],
        ]
    ],

    "BSA" => [
        "name" => "Bachelor of Science in Accountancy",
        "subjects" => [
            ["code" => "AC101", "name" => "Financial Accounting",            "units" => 3],
            ["code" => "AC102", "name" => "Cost Accounting",                 "units" => 3],
            ["code" => "AC103", "name" => "Intermediate Accounting",         "units" => 3],
            ["code" => "AC104", "name" => "Taxation",                        "units" => 3],
            ["code" => "AC105", "name" => "Auditing",                        "units" => 3],
        ]
    ],

    "BSBA" => [
        "name" => "Bachelor of Science in Business Administration",
        "subjects" => [
            ["code" => "BA101", "name" => "Principles of Management",        "units" => 3],
            ["code" => "BA102", "name" => "Marketing Management",            "units" => 3],
            ["code" => "BA103", "name" => "Business Finance",                "units" => 3],
            ["code" => "BA104", "name" => "Human Resource Management",       "units" => 3],
            ["code" => "BA105", "name" => "Operations Management",           "units" => 3],
        ]
    ],

    "BSHM" => [
        "name" => "Bachelor of Science in Hospitality Management",
        "subjects" => [
            ["code" => "HM101", "name" => "Hospitality Operations",          "units" => 3],
            ["code" => "HM102", "name" => "Food and Beverage Service",       "units" => 3],
            ["code" => "HM103", "name" => "Front Office Operations",         "units" => 3],
            ["code" => "HM104", "name" => "Housekeeping Operations",         "units" => 3],
            ["code" => "HM105", "name" => "Tourism Fundamentals",            "units" => 3],
        ]
    ]
];

$timeSlots = [
    ["07:30:00", "09:00:00"],
    ["09:00:00", "10:30:00"],
    ["10:30:00", "12:00:00"],
    ["13:00:00", "14:30:00"],
    ["14:30:00", "16:00:00"]
];

$days = [
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday"
];

$rooms = [
    "LAB101",
    "LAB102",
    "ROOM201",
    "ROOM202",
    "ROOM203",
    "ROOM204",
    "ROOM205"
];

$sections = [
    "BSCS" => "BSCS-1A",
    "BSIT" => "BSIT-1A",
    "BSA"  => "BSA-1A",
    "BSBA" => "BSBA-1A",
    "BSHM" => "BSHM-1A"
];




?>