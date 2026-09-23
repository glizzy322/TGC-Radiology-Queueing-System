from pathlib import Path
from zipfile import ZipFile, ZIP_DEFLATED
from lxml import etree as E
from copy import deepcopy
import re, hashlib
root=Path.cwd()
source=Path(r'C:\Users\Glener\Downloads\DAILY INTERNSHIP LOG 2026.docx')
out=root/'output/documentation/DAILY INTERNSHIP LOG 2026 - Week 1.docx'
md=(root/'output/documentation/Daily-Internship-Log-Week-1-Radiology.md').read_text(encoding='utf-8')
ns={'w':'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
w='{'+ns['w']+'}'
z=ZipFile(source); tree=E.fromstring(z.read('word/document.xml')); body=tree.find(w+'body'); base=list(body)[:-1]; sect=deepcopy(body[-1])
qa=root/'output/internship-qa'; qa.mkdir(exist_ok=True)
(qa/'artifact.md').write_text('Reference: '+str(source)+'\nSHA256: '+hashlib.sha256(source.read_bytes()).hexdigest()+'\nReference rendered by Word: 2 pages. Clone body five times; preserve all other package parts and section geometry. Preserve headings, table grids, borders, signature. Fill top metadata and table values. User approved smaller type and spacing for one day per page.\n',encoding='utf-8')
def txt(el): return ''.join(el.itertext())
def setp(p,text):
    rp=p.find('.//'+w+'rPr'); rp=deepcopy(rp) if rp is not None else E.Element(w+'rPr')
    for child in list(p):
        if child.tag!=w+'pPr': p.remove(child)
    r=E.SubElement(p,w+'r');r.append(rp)
    for i,line in enumerate(text.split('\n')):
        if i: E.SubElement(r,w+'br')
        t=E.SubElement(r,w+'t'); t.text=line;t.set('{http://www.w3.org/XML/1998/namespace}space','preserve')
def cell(c,text):
    ps=c.findall(w+'p'); setp(ps[0],text)
    for p in ps[1:]: c.remove(p)
def field(day,label):
    m=re.search(r'\*\*'+re.escape(label)+r':\*\*\s*(.*?)(?=\n\*\*|\n###|\n---|\Z)',day,re.S)
    return m.group(1).strip()
for c in list(body):body.remove(c)
for idx,day in enumerate(re.split(r'## DAY \d+\s*',md)[1:]):
    elems=deepcopy(base)
    ps=[e for e in elems if e.tag==w+'p']; tables=[e for e in elems if e.tag==w+'tbl']
    vals=[field(day,k) for k in ['Date','Time','Department/Area']]
    for p,label,value in zip(ps[1:4],['Date: ','Time: ','Department/Area: '],vals):setp(p,label+value)
    values=vals+[field(day,'Activities Performed'),field(day,'Outputs Completed').replace('\n- ','\n• ').removeprefix('- '),field(day,'Hours Rendered')]
    for tr,value in zip(tables[0].findall(w+'tr'),values):cell(tr.findall(w+'tc')[1],value)
    for tr,label in zip(tables[1].findall(w+'tr'),['Learning/Knowledge Gained','Skills Developed','Challenges Encountered','Actions Taken','Key Takeaway']):cell(tr.findall(w+'tc')[1],field(day,label))
    for e in elems:
        for sz in e.findall('.//w:sz',ns)+e.findall('.//w:szCs',ns):sz.set(w+'val','19')
        for sp in e.findall('.//w:spacing',ns):
            sp.set(w+'before','0');sp.set(w+'after','0');sp.set(w+'line','220');sp.set(w+'lineRule','auto')
        for h in e.findall('.//w:trHeight',ns):h.set(w+'val','0');h.set(w+'hRule','atLeast')
        for margin in e.findall('.//w:tcMar/w:top',ns)+e.findall('.//w:tcMar/w:bottom',ns):margin.set(w+'w','45')
    # Keep the title bold and recognizable; retain original table grid and section geometry.
    for sz in ps[0].findall('.//w:sz',ns):sz.set(w+'val','24')
    # Retain signature writing space.
    sp=ps[-2].find('w:pPr/w:spacing',ns);sp.set(w+'before','200')
    if idx:
        pb=ps[0].find('w:pPr/w:pageBreakBefore',ns);pb.set(w+'val','1')
    for e in elems:body.append(e)
body.append(sect)
with ZipFile(out,'w',ZIP_DEFLATED) as dest:
    for item in z.infolist():dest.writestr(item,E.tostring(tree,xml_declaration=True,encoding='UTF-8',standalone=True) if item.filename=='word/document.xml' else z.read(item.filename))
with ZipFile(out) as final:
    assert all(final.read(n)==z.read(n) for n in z.namelist() if n!='word/document.xml')
print(out)
