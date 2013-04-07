@for /r . %%a in (.) do @if exist "%%a\_SVN" rd /s /q "%%a\_SVN"
@Rem for /r . %%a in (.) do @if exist "%%a\_SVN" @echo "%%a\_SVN"
@for /r . %%a in (.) do @if exist "%%a\.SVN" rd /s /q "%%a\.SVN"
@Rem for /r . %%a in (.) do @if exist "%%a\.SVN" @echo "%%a\.SVN"
@echo Mission Completed.
@pause