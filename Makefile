BUILD_DIR ?= build/
DOCS_DIR ?= phpdocs/

phpdocs: ./vendor/phpdocumentor/phpdocumentor/bin/phpdoc
	mkdir -p $(BUILD_DIR)/$(DOCS_DIR)
	./vendor/phpdocumentor/phpdocumentor/bin/phpdoc run -t $(BUILD_DIR)/$(DOCS_DIR) -d src/AlternCApi -e "php"
