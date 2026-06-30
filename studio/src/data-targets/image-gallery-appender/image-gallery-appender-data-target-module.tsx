import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { ImageGalleryAppenderDataTargetSettings } from "./image-gallery-appender-data-target-settings";

export const ImageGalleryAppenderDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "imageGalleryAppender",
            label: "Image Gallery Appender",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <ImageGalleryAppenderDataTargetSettings {...props} />;
            },
        });
    },
};
