from copy import deepcopy
from datetime import datetime
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile
import re

from lxml import etree as E


ROOT = Path(__file__).resolve().parents[2]
SOURCE = Path(r"C:\Users\Glener\Downloads\DAILY INTERNSHIP LOG 2026.docx")
OUTPUT = ROOT / "output" / "documentation" / "DAILY INTERNSHIP LOG 2026 - Weeks 1 to 5.docx"
WEEK_1_TEXT = ROOT / "output" / "documentation" / "Daily-Internship-Log-Week-1-Radiology.md"

NS = {"w": "http://schemas.openxmlformats.org/wordprocessingml/2006/main"}
W = "{" + NS["w"] + "}"

WEEK_2 = [
    {
        "date": "July 28, 2026",
        "activities": (
            "1. Assisted in preparing and printing bathroom reminder signs needed by the hospital.\n"
            "2. Reviewed reminders such as not flushing tissues and other proper bathroom practices.\n"
            "3. Checked the sign layouts and wording before printing multiple copies.\n"
            "4. Monitored the printer, paper, and ink while handling the large printing request.\n"
            "5. Continued discussing the TGMCI Radiology Queueing System during available time."
        ),
        "outputs": (
            "• Prepared bathroom reminder signs for printing.\n"
            "• Printed multiple copies of the required hospital signs.\n"
            "• Continued recording ideas for the TGMCI Radiology Queueing System."
        ),
        "learning": "I learned that hospital signs must be clear, readable, and carefully checked because they communicate important reminders to patients, visitors, and staff.",
        "skills": "Document checking, high-volume printing, attention to detail, organization, and time management.",
        "challenges": "The large number of signs required careful monitoring to avoid printing errors and wasting paper or ink.",
        "actions": "I checked the layout and wording before printing and regularly monitored the printer and supplies.",
        "takeaway": "Careful preparation is important when producing many copies of materials for hospital use.",
    },
    {
        "date": "July 29, 2026",
        "activities": (
            "1. Continued printing bathroom signs containing reminders required for hospital compliance with DOH guidance.\n"
            "2. Printed signs about not flushing tissues and other bathroom rules.\n"
            "3. Checked completed pages for clear text, correct alignment, and consistent print quality.\n"
            "4. Sorted the printed signs to prepare them for lamination.\n"
            "5. Discussed the queueing system with the team when the printing workload allowed."
        ),
        "outputs": (
            "• Completed another batch of bathroom signs.\n"
            "• Sorted the printed pages for the next step.\n"
            "• Continued discussing the proposed queueing system."
        ),
        "learning": "I learned how to maintain consistency and accuracy while completing a high-volume printing task.",
        "skills": "Printer operation, quality checking, sorting, attention to detail, and task prioritization.",
        "challenges": "Some pages needed to be checked carefully to make sure the text and print quality were acceptable.",
        "actions": "I reviewed the printed pages as each batch was completed and separated any page that needed to be printed again.",
        "takeaway": "Quality checks help prevent errors from continuing throughout a large printing job.",
    },
    {
        "date": "July 30, 2026",
        "activities": (
            "1. Helped complete the printing of almost 100 pages of bathroom reminder signs.\n"
            "2. Learned the basic operation and safety precautions of the laminating machine.\n"
            "3. Prepared the printed signs and laminating materials.\n"
            "4. Practiced placing each sign properly inside a laminating pouch.\n"
            "5. Continued a short discussion about the TGMCI Radiology Queueing System."
        ),
        "outputs": (
            "• Helped complete almost 100 printed pages of bathroom signs.\n"
            "• Prepared the signs and materials for lamination.\n"
            "• Learned the basic laminating procedure."
        ),
        "learning": "I learned how lamination protects printed materials and how proper placement helps prevent wrinkles, bubbles, and uneven edges.",
        "skills": "Laminating, material preparation, equipment handling, patience, and accuracy.",
        "challenges": "I was unfamiliar with the laminating machine and needed to avoid misaligned sheets and damaged pouches.",
        "actions": "I followed the instructions carefully and practiced with guidance before handling more of the printed signs.",
        "takeaway": "Using office equipment properly requires patience, careful positioning, and attention to safety.",
    },
    {
        "date": "July 31, 2026",
        "activities": (
            "1. Continued laminating the printed bathroom signs.\n"
            "2. Checked each laminated sign for wrinkles, bubbles, or incomplete sealing.\n"
            "3. Sorted and organized the finished signs.\n"
            "4. Learned how to identify and correct simple printer problems during the remaining printing tasks.\n"
            "5. Continued exchanging ideas about the queueing system during available time."
        ),
        "outputs": (
            "• Laminated and checked another batch of bathroom signs.\n"
            "• Organized the completed signs.\n"
            "• Practiced basic printer troubleshooting."
        ),
        "learning": "I learned how to inspect laminated materials and how to check simple printer issues such as paper placement, paper jams, and connection problems.",
        "skills": "Laminating, quality inspection, organization, printer troubleshooting, and problem-solving.",
        "challenges": "The repetitive workload required concentration, and minor printer issues could interrupt the printing process.",
        "actions": "I checked each laminated sign and followed basic troubleshooting steps when the printer did not operate normally.",
        "takeaway": "Regular checking and simple troubleshooting can keep a large office task moving efficiently.",
    },
    {
        "date": "August 3, 2026",
        "activities": (
            "1. Helped finish laminating the remaining bathroom signs.\n"
            "2. Inspected and organized the completed signs for hospital use.\n"
            "3. Reviewed the printing and laminating work completed during the week.\n"
            "4. Practiced solving simple printer problems encountered during printing.\n"
            "5. Continued discussing the direction and possible features of the TGMCI Radiology Queueing System."
        ),
        "outputs": (
            "• Completed the printing and lamination of the bathroom signs.\n"
            "• Organized the finished materials for distribution.\n"
            "• Continued planning the TGMCI Radiology Queueing System.\n"
            "• Improved my understanding of basic printer troubleshooting."
        ),
        "learning": "I learned how printing, lamination, quality checking, and organization work together when preparing materials for hospital use.",
        "skills": "High-volume printing, laminating, quality control, basic printer troubleshooting, teamwork, and time management.",
        "challenges": "The printing and laminating workload left limited time to focus on the queueing system.",
        "actions": "I prioritized the hospital's urgent printing requirements and used available time to continue discussing the system with the team.",
        "takeaway": "Work priorities can change, so it is important to complete urgent tasks while continuing long-term projects whenever time is available.",
    },
]

WEEK_3 = [
    {
        "date": "August 4, 2026",
        "activities": (
            "1. Sorted and prepared the laminated restroom signs for installation by floor.\n"
            "2. Began placing the signs in hospital restrooms, including restrooms inside patient rooms.\n"
            "3. Installed signs on the 7th and 6th floors.\n"
            "4. Learned from an IT department head how to arrange, crimp, and check a UTP cable with an RJ45 connector.\n"
            "5. Began planning the prototype and activity diagram for the TGMCI Radiology Queueing System."
        ),
        "outputs": (
            "• Installed laminated restroom signs on the 7th and 6th floors.\n"
            "• Learned the basic UTP-to-RJ45 cable-crimping procedure.\n"
            "• Identified the main steps needed for the system prototype and activity diagram."
        ),
        "learning": "I learned the correct order of UTP wires, how an RJ45 connector is crimped, and why the finished cable must be checked before use.",
        "skills": "UTP cable preparation, RJ45 crimping, organization, installation, and workflow analysis.",
        "challenges": "There were many restrooms and patient rooms to check while moving through each floor.",
        "actions": "I followed a floor-by-floor route and checked each location after placing the signs.",
        "takeaway": "A systematic route helps complete hospital-wide work accurately and efficiently.",
    },
    {
        "date": "August 5, 2026",
        "activities": (
            "1. Continued installing laminated restroom signs throughout the hospital.\n"
            "2. Placed signs in common restrooms and patient-room restrooms on the 5th and 4th floors.\n"
            "3. Checked the signs for clear visibility and proper placement.\n"
            "4. Started building the prototype of the TGMCI Radiology Queueing System.\n"
            "5. Outlined the system flow from selecting a procedure and generating a queue number to patient waiting."
        ),
        "outputs": (
            "• Installed and checked signs on the 5th and 4th floors.\n"
            "• Developed the initial reception and queue-number parts of the prototype.\n"
            "• Organized the remaining signs for the next floors."
        ),
        "learning": "I learned how an activity diagram can guide prototype development by showing the correct order of actions in the queueing process.",
        "skills": "Prototype development, sign installation, activity-diagram creation, sequencing, and time management.",
        "challenges": "The physical installation work had to be balanced with the time needed to prepare the system diagram.",
        "actions": "I completed the assigned floors first and used the remaining time to continue the diagram.",
        "takeaway": "Dividing time between field work and system documentation helps both tasks continue steadily.",
    },
    {
        "date": "August 6, 2026",
        "activities": (
            "1. Continued placing laminated signs in hospital restrooms and patient rooms.\n"
            "2. Installed and checked the signs on the 3rd and 2nd floors.\n"
            "3. Confirmed that the reminders were easy to see and read.\n"
            "4. Continued developing the queueing-system prototype and its required diagrams.\n"
            "5. Helped diagnose and fix simple technical issues reported by another hospital department."
        ),
        "outputs": (
            "• Completed sign installation on the 3rd and 2nd floors.\n"
            "• Added queue-management functions and diagram details to the prototype work.\n"
            "• Resolved simple technical concerns with guidance when needed."
        ),
        "learning": "I learned how to apply basic troubleshooting steps while continuing to connect the staff dashboard, assigned room, and public display in the prototype.",
        "skills": "Basic technical troubleshooting, prototype development, process mapping, diagramming, and system analysis.",
        "challenges": "The diagram needed to show the queue flow clearly without leaving out important staff actions.",
        "actions": "I reviewed the actual queueing steps and arranged them in the correct sequence before adding them to the diagram.",
        "takeaway": "System diagrams are clearer when they are based on the actual steps followed by users.",
    },
    {
        "date": "August 7, 2026",
        "activities": (
            "1. Continued the floor-by-floor installation of laminated restroom signs.\n"
            "2. Placed the remaining signs in restrooms and patient rooms on the 1st and ground floors.\n"
            "3. Checked the installed signs for proper placement and readability.\n"
            "4. Continued building and checking the queueing-system prototype.\n"
            "5. Assisted other departments by independently handling simple computer and peripheral issues."
        ),
        "outputs": (
            "• Completed sign installation from the 7th floor down to the ground floor.\n"
            "• Tested the developing prototype and reviewed its activity diagram.\n"
            "• Completed simple technical support tasks for other departments."
        ),
        "learning": "I learned the value of testing prototype functions and applying a clear troubleshooting process before considering a task complete.",
        "skills": "Prototype testing, technical support, installation, diagram review, teamwork, and attention to detail.",
        "challenges": "Completing the lower floors required making sure that no restroom or patient-room location was overlooked.",
        "actions": "I checked each assigned location and reviewed the completed floor coverage with the team.",
        "takeaway": "Final checks help confirm that all required hospital areas and system steps have been covered.",
    },
    {
        "date": "August 10, 2026",
        "activities": (
            "1. Reviewed the completed restroom-sign installation from the 7th floor to the ground floor.\n"
            "2. Confirmed that signs were present in the required common and patient-room restrooms.\n"
            "3. Continued developing and improving the TGMCI Radiology Queueing System prototype.\n"
            "4. Refined the activity diagram and other diagrams needed to document the system.\n"
            "5. Reviewed the week's prototype work and the simple technical issues handled for other departments."
        ),
        "outputs": (
            "• Confirmed the hospital-wide distribution of the laminated restroom signs.\n"
            "• Improved the queueing-system prototype and activity diagram.\n"
            "• Organized the prototype and system-diagram work for further review."
        ),
        "learning": "I learned that building a prototype, reviewing diagrams, and solving real technical issues develop both system-design and support skills.",
        "skills": "Prototype development, documentation, diagramming, troubleshooting, organization, and quality checking.",
        "challenges": "The diagrams needed to match the actual radiology queueing workflow and remain easy to understand.",
        "actions": "I compared the diagram steps with the system process and revised sections that needed clearer flow.",
        "takeaway": "Accurate diagrams provide a useful guide for understanding and improving the queueing system.",
    },
]

WEEK_4 = [
    {
        "date": "August 11, 2026",
        "activities": (
            "1. Attended a practical lesson conducted by one of the IT Department heads on crimping a UTP cable to an RJ45 connector.\n"
            "2. Learned the correct wire arrangement and the basic tools used for cable preparation.\n"
            "3. Practiced stripping the cable jacket, arranging the wires, and inserting them into the connector.\n"
            "4. Observed the proper use of the crimping tool and cable tester.\n"
            "5. Discussed the initial plan for the TGMCI Radiology Queueing System prototype."
        ),
        "outputs": (
            "• Practiced the basic steps for crimping a UTP cable to an RJ45 connector.\n"
            "• Identified the tools and materials used in network-cable preparation.\n"
            "• Prepared an initial plan for the queueing-system prototype."
        ),
        "learning": "I learned the correct sequence for preparing, arranging, crimping, and testing a UTP cable with an RJ45 connector.",
        "skills": "Cable preparation, RJ45 crimping, tool handling, observation, and technical planning.",
        "challenges": "The small wires had to remain in the correct order and position before being inserted into the connector.",
        "actions": "I followed the demonstration carefully and checked the wire arrangement before using the crimping tool.",
        "takeaway": "Accurate wire arrangement and careful tool use are essential when making a reliable network cable.",
    },
    {
        "date": "August 12, 2026",
        "activities": (
            "1. Continued practicing how to crimp UTP cables to RJ45 connectors.\n"
            "2. Used a cable tester to check whether the completed cable was working correctly.\n"
            "3. Began building the prototype of the TGMCI Radiology Queueing System.\n"
            "4. Prepared the initial project structure and basic interface screens.\n"
            "5. Reviewed how reception, queue management, and the public display should connect."
        ),
        "outputs": (
            "• Produced and tested a practice network cable.\n"
            "• Started the queueing-system prototype.\n"
            "• Prepared the initial system structure and interface flow."
        ),
        "learning": "I learned how a cable tester confirms the connections in a crimped cable and how a system prototype turns diagrams into a working model.",
        "skills": "Cable testing, troubleshooting, prototyping, interface planning, and system organization.",
        "challenges": "The prototype needed a clear structure so that its major queueing functions could work together.",
        "actions": "I organized the prototype by its main functions and tested each cable connection before considering it complete.",
        "takeaway": "Testing each part early helps identify problems before more features are added.",
    },
    {
        "date": "August 13, 2026",
        "activities": (
            "1. Continued developing the queueing-system prototype.\n"
            "2. Worked on the reception process for selecting a procedure, patient type, and generating a queue number.\n"
            "3. Connected the generated queue information to the queue-management process.\n"
            "4. Assisted another hospital department with a simple technical issue.\n"
            "5. Checked basic connections and settings to identify the cause of the issue."
        ),
        "outputs": (
            "• Added initial reception functions to the prototype.\n"
            "• Connected basic queue-generation and management steps.\n"
            "• Helped resolve a simple technical issue in another department."
        ),
        "learning": "I learned how the reception process supplies the information needed by queue management and how basic checks can solve common technical problems.",
        "skills": "Prototype development, process integration, basic technical support, troubleshooting, and communication.",
        "challenges": "I needed to understand the reported technical problem while continuing the scheduled prototype work.",
        "actions": "I asked about the symptoms, checked the simplest possible causes, and returned to the prototype after resolving the issue.",
        "takeaway": "A step-by-step troubleshooting process makes simple technical issues easier to identify and fix.",
    },
    {
        "date": "August 14, 2026",
        "activities": (
            "1. Continued building and improving the queueing-system prototype.\n"
            "2. Worked on the staff queue-management screen and patient-calling process.\n"
            "3. Connected the called queue number to the public-display prototype.\n"
            "4. Responded to another simple technical concern from a hospital department.\n"
            "5. Performed basic checks and applied an appropriate solution."
        ),
        "outputs": (
            "• Added basic queue-management and patient-calling functions.\n"
            "• Connected called queue numbers to the public-display prototype.\n"
            "• Assisted with a basic technical concern in another department."
        ),
        "learning": "I learned how the staff dashboard and public display must share the same queue information and how to communicate clearly while providing technical support.",
        "skills": "System integration, interface development, troubleshooting, communication, and problem-solving.",
        "challenges": "The displayed queue information needed to update correctly after a patient was called.",
        "actions": "I reviewed the data flow between the management screen and public display and tested the calling process repeatedly.",
        "takeaway": "Connected system features should be tested together to confirm that information remains consistent.",
    },
    {
        "date": "August 17, 2026",
        "activities": (
            "1. Continued refining the TGMCI Radiology Queueing System prototype.\n"
            "2. Reviewed the reception, queue-management, and public-display workflow.\n"
            "3. Tested the prototype and recorded functions that needed improvement.\n"
            "4. Applied lessons from the week's basic technical-support tasks.\n"
            "5. Organized the prototype work and prepared it for the next stage of development."
        ),
        "outputs": (
            "• Completed the first working stage of the queueing-system prototype.\n"
            "• Tested the main workflow and listed areas for improvement.\n"
            "• Organized the prototype files for continued development."
        ),
        "learning": "I learned that a prototype should be tested as a complete workflow so that missing steps and connections can be found early.",
        "skills": "Prototype testing, workflow review, debugging, documentation, and organization.",
        "challenges": "Some prototype functions still required adjustment after the complete workflow was tested.",
        "actions": "I tested the main process from queue generation to public display and recorded the issues that needed further work.",
        "takeaway": "A working prototype provides a practical basis for testing, feedback, and continued system improvement.",
    },
]

WEEK_5 = [
    {
        "date": "August 18, 2026",
        "activities": (
            "1. Helped prepare and set up the sound system for a hospital seminar.\n"
            "2. Assisted in arranging the speakers, microphones, cables, and other audio equipment.\n"
            "3. Checked the equipment connections and performed an audio test before the seminar.\n"
            "4. Was assigned to operate a camera and take photographs during the event.\n"
            "5. Continued implementing the initial parts of the TGMCI Radiology Queueing System."
        ),
        "outputs": (
            "• Helped complete and test the seminar sound-system setup.\n"
            "• Took photographs to document the seminar.\n"
            "• Continued the initial implementation of the queueing system."
        ),
        "learning": "I learned how proper equipment placement, cable connections, and sound checks help an event run smoothly and how photographs provide useful documentation.",
        "skills": "Audio-equipment setup, camera handling, event photography, coordination, and system implementation.",
        "challenges": "The sound equipment and camera had to be ready before the seminar began.",
        "actions": "I checked the audio connections, tested the sound, prepared the camera, and confirmed that the equipment was working.",
        "takeaway": "Preparing and testing event equipment early helps avoid interruptions during a seminar.",
    },
    {
        "date": "August 19, 2026",
        "activities": (
            "1. Assisted with another sound-system setup for a hospital seminar or event.\n"
            "2. Helped position the speakers and microphones and kept the cables organized.\n"
            "3. Used the camera to photograph the speakers, participants, and important activities.\n"
            "4. Continued creating the reception and queue-generation functions of the queueing system.\n"
            "5. Reviewed the captured photographs and organized the usable files."
        ),
        "outputs": (
            "• Prepared and tested the audio equipment for the event.\n"
            "• Captured and organized seminar photographs.\n"
            "• Continued implementing reception and queue-generation functions."
        ),
        "learning": "I learned how to select useful event photographs while monitoring the program and how reception data begins the queueing workflow.",
        "skills": "Event support, photography, file organization, interface development, and attention to detail.",
        "challenges": "I needed to capture important moments without interrupting the seminar or blocking the participants' view.",
        "actions": "I moved carefully around the venue, chose appropriate camera angles, and organized the photographs after the event.",
        "takeaway": "Good event documentation requires preparation, awareness of the surroundings, and careful timing.",
    },
    {
        "date": "August 20, 2026",
        "activities": (
            "1. Continued assisting with sound-system preparation for hospital activities.\n"
            "2. Operated the camera and took photographs during a seminar.\n"
            "3. Continued implementing the queue-management and patient-calling parts of the system.\n"
            "4. Learned how to inspect and safely clear a simple printer paper jam.\n"
            "5. Checked the paper path, paper alignment, and printer rollers after clearing the jam."
        ),
        "outputs": (
            "• Supported the seminar audio setup and photo documentation.\n"
            "• Added initial queue-management functions to the system.\n"
            "• Practiced resolving a basic printer paper jam."
        ),
        "learning": "I learned that paper jams should be cleared carefully and that the paper path and alignment should be checked before printing again.",
        "skills": "Camera operation, audio support, system implementation, printer troubleshooting, and safe equipment handling.",
        "challenges": "Removing jammed paper without tearing it or leaving pieces inside the printer required patience.",
        "actions": "I followed the correct steps, removed the paper gently, checked the paper path, and tested the printer afterward.",
        "takeaway": "Careful inspection and testing help prevent a simple printer problem from becoming more serious.",
    },
    {
        "date": "August 21, 2026",
        "activities": (
            "1. Helped set up and test sound equipment for another hospital event.\n"
            "2. Took photographs of the event using the assigned camera.\n"
            "3. Continued implementing the public-display and queue-update functions of the system.\n"
            "4. Learned how to identify common printer ink problems and perform basic printer resetting.\n"
            "5. Tested the printer after checking the ink and completing the reset procedure."
        ),
        "outputs": (
            "• Completed event audio support and photo documentation.\n"
            "• Continued implementing the public-display workflow.\n"
            "• Practiced checking ink problems and resetting a printer."
        ),
        "learning": "I learned that faded or missing print can be related to ink levels, cartridge placement, or printer settings and that a reset may restore normal operation.",
        "skills": "Event photography, sound-system setup, public-display development, printer maintenance, and troubleshooting.",
        "challenges": "The cause of a printer problem was not always immediately clear from the first symptom.",
        "actions": "I checked the ink supply and cartridge placement, reviewed the printer status, performed a reset, and printed a test page.",
        "takeaway": "Printer troubleshooting is more effective when possible causes are checked one at a time.",
    },
    {
        "date": "August 24, 2026",
        "activities": (
            "1. Assisted with the preparation of sound and camera equipment for hospital activities.\n"
            "2. Reviewed and organized photographs taken during the week's seminars and events.\n"
            "3. Continued integrating and testing the main parts of the TGMCI Radiology Queueing System.\n"
            "4. Responded to simple technical issues affecting computers in other hospital departments.\n"
            "5. Checked basic power, cable, peripheral, network, and system settings based on the reported issue."
        ),
        "outputs": (
            "• Organized the week's event photographs and equipment.\n"
            "• Integrated and tested key queueing-system functions.\n"
            "• Assisted hospital staff with basic computer-related issues."
        ),
        "learning": "I learned that basic computer troubleshooting begins by gathering information and checking simple causes before making changes.",
        "skills": "PC troubleshooting, user support, system testing, equipment organization, and communication.",
        "challenges": "Different computer issues produced different symptoms and required careful checking before applying a solution.",
        "actions": "I asked the user about the problem, inspected basic connections and settings, applied an appropriate fix, and confirmed the result.",
        "takeaway": "A logical troubleshooting process helps resolve common computer issues safely and efficiently.",
    },
]


def element_text(element):
    return "".join(element.itertext()).strip()


def replace_paragraph_text(paragraph, text):
    run_properties = paragraph.find(".//" + W + "rPr")
    run_properties = deepcopy(run_properties) if run_properties is not None else E.Element(W + "rPr")
    for child in list(paragraph):
        if child.tag != W + "pPr":
            paragraph.remove(child)
    run = E.SubElement(paragraph, W + "r")
    run.append(run_properties)
    for index, line in enumerate(text.split("\n")):
        if index:
            E.SubElement(run, W + "br")
        node = E.SubElement(run, W + "t")
        node.text = line
        node.set("{http://www.w3.org/XML/1998/namespace}space", "preserve")


def replace_cell_text(table_cell, text):
    paragraphs = table_cell.findall(W + "p")
    replace_paragraph_text(paragraphs[0], text)
    for paragraph in paragraphs[1:]:
        table_cell.remove(paragraph)


def markdown_field(day_text, label):
    match = re.search(
        r"\*\*" + re.escape(label) + r":\*\*\s*(.*?)(?=\n\*\*|\n###|\n---|\Z)",
        day_text,
        re.S,
    )
    if not match:
        raise RuntimeError(f"Missing Week 1 field: {label}")
    return match.group(1).strip()


week_1_days = []
week_1_source = WEEK_1_TEXT.read_text(encoding="utf-8")
for day_text in re.split(r"## DAY \d+\s*", week_1_source)[1:]:
    week_1_days.append(
        {
            "date": markdown_field(day_text, "Date"),
            "department": markdown_field(day_text, "Department/Area"),
            "activities": markdown_field(day_text, "Activities Performed"),
            "outputs": markdown_field(day_text, "Outputs Completed").replace("\n- ", "\n• ").removeprefix("- "),
            "learning": markdown_field(day_text, "Learning/Knowledge Gained"),
            "skills": markdown_field(day_text, "Skills Developed"),
            "challenges": markdown_field(day_text, "Challenges Encountered"),
            "actions": markdown_field(day_text, "Actions Taken"),
            "takeaway": markdown_field(day_text, "Key Takeaway"),
        }
    )

# The internship began on Tuesday, July 21. Continue the entries on weekdays
# only, carrying Day 5 to the following Monday instead of using Saturday.
week_1_weekday_dates = [
    "July 21, 2026",
    "July 22, 2026",
    "July 23, 2026",
    "July 24, 2026",
    "July 27, 2026",
]
for day, corrected_date in zip(week_1_days, week_1_weekday_dates):
    day["date"] = corrected_date

for day in WEEK_2 + WEEK_3 + WEEK_4 + WEEK_5:
    day["department"] = "IT Department"

ALL_DAYS = week_1_days + WEEK_2 + WEEK_3 + WEEK_4 + WEEK_5
if any(datetime.strptime(day["date"], "%B %d, %Y").weekday() >= 5 for day in ALL_DAYS):
    raise RuntimeError("A Saturday or Sunday date remains in the internship log")


with ZipFile(SOURCE) as source_package:
    document_tree = E.fromstring(source_package.read("word/document.xml"))
    body = document_tree.find(W + "body")
    children = list(body)
    page_template = children[:-1]
    section_properties = children[-1]
    for child in children:
        body.remove(child)

    for day_index, day in enumerate(ALL_DAYS):
        page = deepcopy(page_template)
        paragraphs = [element for element in page if element.tag == W + "p"]
        tables = [element for element in page if element.tag == W + "tbl"]

        replace_paragraph_text(paragraphs[1], f"Date: {day['date']}")
        replace_paragraph_text(paragraphs[2], "Time: 8:00 AM - 5:00 PM")
        replace_paragraph_text(paragraphs[3], f"Department/Area: {day['department']}")

        accomplishments = [
            day["date"],
            "8:00 AM - 5:00 PM",
            day["department"],
            day["activities"],
            day["outputs"],
            "8 hours",
        ]
        for row, value in zip(tables[0].findall(W + "tr"), accomplishments):
            replace_cell_text(row.findall(W + "tc")[1], value)

        learning = [
            day["learning"],
            day["skills"],
            day["challenges"],
            day["actions"],
            day["takeaway"],
        ]
        for row, value in zip(tables[1].findall(W + "tr"), learning):
            replace_cell_text(row.findall(W + "tc")[1], value)
        replace_cell_text(
            tables[1].findall(W + "tr")[0].findall(W + "tc")[0],
            "Learning/\nKnowledge Gained",
        )

        # A4 content width after a 3.18 cm left margin and 2.54 cm right margin.
        table_widths = (2700, 5963)
        for table in tables:
            table_width = table.find("w:tblPr/w:tblW", NS)
            table_width.set(W + "w", "8663")
            table_width.set(W + "type", "dxa")
            for column_index, grid_column in enumerate(table.findall(".//w:gridCol", NS)):
                grid_column.set(W + "w", str(table_widths[column_index % 2]))
            for row in table.findall(W + "tr"):
                for cell_index, table_cell in enumerate(row.findall(W + "tc")):
                    cell_width = table_cell.find("w:tcPr/w:tcW", NS)
                    if cell_width is not None:
                        cell_width.set(W + "w", str(table_widths[cell_index]))
                        cell_width.set(W + "type", "dxa")

        for element in page:
            for size in element.findall(".//w:sz", NS) + element.findall(".//w:szCs", NS):
                size.set(W + "val", "19")
            for spacing in element.findall(".//w:spacing", NS):
                spacing.set(W + "before", "0")
                spacing.set(W + "after", "0")
                spacing.set(W + "line", "220")
                spacing.set(W + "lineRule", "auto")
            for row_height in element.findall(".//w:trHeight", NS):
                row_height.set(W + "val", "0")
                row_height.set(W + "hRule", "atLeast")
            margins = element.findall(".//w:tcMar/w:top", NS) + element.findall(".//w:tcMar/w:bottom", NS)
            for margin in margins:
                margin.set(W + "w", "45")

        for size in paragraphs[0].findall(".//w:sz", NS):
            size.set(W + "val", "24")
        signature_spacing = paragraphs[-2].find("w:pPr/w:spacing", NS)
        signature_spacing.set(W + "before", "200")
        if day_index:
            page_break = paragraphs[0].find("w:pPr/w:pageBreakBefore", NS)
            page_break.set(W + "val", "1")

        for element in page:
            body.append(element)

    page_size = section_properties.find(W + "pgSz")
    page_size.set(W + "w", "11906")
    page_size.set(W + "h", "16838")
    page_size.set(W + "orient", "portrait")
    page_margins = section_properties.find(W + "pgMar")
    page_margins.set(W + "left", "1803")
    page_margins.set(W + "right", "1440")
    page_margins.set(W + "top", "1440")
    page_margins.set(W + "bottom", "1440")
    body.append(section_properties)

    with ZipFile(OUTPUT, "w", ZIP_DEFLATED) as output_package:
        for item in source_package.infolist():
            if item.filename == "word/document.xml":
                payload = E.tostring(
                    document_tree,
                    xml_declaration=True,
                    encoding="UTF-8",
                    standalone=True,
                )
            else:
                payload = source_package.read(item.filename)
            output_package.writestr(item, payload)

with ZipFile(SOURCE) as source_package, ZipFile(OUTPUT) as output_package:
    preserved_parts = [name for name in source_package.namelist() if name != "word/document.xml"]
    if not all(output_package.read(name) == source_package.read(name) for name in preserved_parts):
        raise RuntimeError("A preserve-only DOCX package part changed")

print(OUTPUT)
