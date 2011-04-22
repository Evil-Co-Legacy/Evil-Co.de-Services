@echo off
REM Compiles languages
REM Use: compileLanguage.bat <languagecode>
msgfmt -o %1.mo %1.po