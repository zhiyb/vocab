HTML	= index.html cards.php

.PHONY: all
all: js css $(HTML)

.PHONY: js css
js css:
	$(MAKE) -C $@

%.html: %-dev.html
	sed 's/\(js\|css\)\/src\/\(.\+\)\.\(js\|css\)/\1\/\2.min.\3/' "$<" > "$@"

%.php: %-dev.php
	sed 's/\(js\|css\)\/src\/\(.\+\)\.\(js\|css\)/\1\/\2.min.\3/' "$<" > "$@"

.PHONY: clean
clean:
	rm -f $(HTML)
