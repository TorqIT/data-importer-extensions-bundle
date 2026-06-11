import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const AsCountryCodeTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "asCountryCode",
            label: "As Country Code",
            group: "dataTypes",
            renderSettings() {
                return null;
            },
        });
    },
};
