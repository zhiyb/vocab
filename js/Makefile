JS	:= $(wildcard src/*.js)
CSS	:= $(wildcard src/*.css)

MIN	:= $(JS:src/%.js=%.min.js)
MIN	+= $(CSS:src/%.css=%.min.css)

.PHONY: all
all: $(MIN)

.DELETE_ON_ERROR:

%.min.js: src/%.js
	yui-compressor "$<" --type js > "$@"

%.min.css: src/%.css
	yui-compressor "$<" --type css > "$@"

.PHONY: clean
clean:
	rm -f $(MIN)
