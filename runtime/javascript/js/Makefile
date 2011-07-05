SRC_DIR = src
TEST_DIR = test
BUILD_DIR = build
BIN_DIR = bin

PREFIX = .
DIST_DIR = ${PREFIX}/dist

BASE_FILES = ${SRC_DIR}/package.js\
  ${SRC_DIR}/core.js\
  ${SRC_DIR}/event.js\
  ${SRC_DIR}/player.js\
  ${SRC_DIR}/context.js\
  ${SRC_DIR}/avm.js\
  ${SRC_DIR}/geom/rect.js\
  ${SRC_DIR}/geom/matrix.js\
  ${SRC_DIR}/geom/cxform.js\
  ${SRC_DIR}/display/display.js\
  ${SRC_DIR}/display/bitmap.js\
  ${SRC_DIR}/display/shape.js\
  ${SRC_DIR}/display/font.js\
  ${SRC_DIR}/display/text.js\
  ${SRC_DIR}/display/display_container.js\
  ${SRC_DIR}/display/sprite.js\
  ${SRC_DIR}/display/stage.js\
  ${SRC_DIR}/renderer/svg.js\
  ${SRC_DIR}/renderer/canvas.js

MODULES = ${SRC_DIR}/intro.js\
  ${BASE_FILES}\
  ${SRC_DIR}/outro.js

VERSION = $(shell cat version.txt)
VER = sed "s/@VERSION/${VERSION}/"

BUILD = ${DIST_DIR}/flashforward.js
BUILD_MIN = ${DIST_DIR}/flashforward.min.js
BIN=${BIN_DIR}/${VERSION}/flashforward.min.js

COMPILER = ${BUILD_DIR}/compiler.jar
TEMP = ${BUILD}.tmp.js

all: build min

recompile: clean all

${DIST_DIR}:
	@@mkdir -p ${DIST_DIR}

${BIN_DIR}:
	@@mkdir -p ${BIN_DIR}/${VERSION}

build: ${BUILD}

${BUILD}: ${MODULES} | ${DIST_DIR}
	@@echo "Building" ${BUILD}

	@@cat ${MODULES} | \
		${VER} > ${BUILD}; 
    
min: build ${BUILD_MIN}

${BUILD_MIN}: ${BUILD}
	@@echo "Minifiy" ${BUILD_MIN}

	cat ${BUILD} | sed "s/\"use strict\"//" > ${TEMP}

#	 java -jar ${COMPILER} --js ${TEMP} --js_output_file ${BUILD_MIN} --compilation_level ADVANCED_OPTIMIZATIONS 
	java -jar ${COMPILER} --js ${TEMP} --js_output_file ${BUILD_MIN}

	rm -rf ${TEMP}

install: min ${BIN_DIR}
	cp ${BUILD_MIN} ${BIN}

clean:
	@@echo "Removing Distribution directory:" ${DIST_DIR}
	@@rm -rf ${DIST_DIR}
